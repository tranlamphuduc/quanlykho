<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class StockOut extends Model
{
    protected $fillable = ['code', 'warehouse_id', 'user_id', 'customer_name', 'total_amount', 'note', 'status'];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(StockOutDetail::class);
    }

    public static function generateCode(): string
    {
        $prefix = 'PX' . date('Ymd');
        $last = self::where('code', 'like', $prefix . '%')->orderBy('code', 'desc')->first();
        $nextNum = $last ? (int)substr($last->code, -4) + 1 : 1;
        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    public static function createWithDetails(array $data, array $details): self
    {
        return DB::transaction(function () use ($data, $details) {
            // Check stock first
            foreach ($details as $detail) {
                $currentStock = Inventory::getStock($detail['product_id'], $data['warehouse_id']);
                if ($currentStock < $detail['quantity']) {
                    throw new \Exception("Sản phẩm không đủ số lượng trong kho!");
                }
            }

            $stockOut = self::create($data);
            $totalAmount = 0;

            foreach ($details as $detail) {
                $stockOut->details()->create($detail);
                $totalAmount += $detail['quantity'] * $detail['unit_price'];
                
                Inventory::updateStock(
                    $detail['product_id'],
                    $data['warehouse_id'],
                    $detail['quantity'],
                    false
                );
            }

            $stockOut->update(['total_amount' => $totalAmount]);
            return $stockOut;
        });
    }

    public static function getMonthlyStats($year = null)
    {
        $year = $year ?? date('Y');
        return self::selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(total_amount) as total')
            ->whereYear('created_at', $year)
            ->where('status', 'completed')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');
    }

    public static function getTopProducts($limit = 5, $year = null)
    {
        $year = $year ?? date('Y');
        return StockOutDetail::join('stock_outs', 'stock_out_details.stock_out_id', '=', 'stock_outs.id')
            ->join('products', 'stock_out_details.product_id', '=', 'products.id')
            ->whereYear('stock_outs.created_at', $year)
            ->where('stock_outs.status', 'completed')
            ->selectRaw('products.id, products.code, products.name, SUM(stock_out_details.quantity) as total_quantity')
            ->groupBy('products.id', 'products.code', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }
}
