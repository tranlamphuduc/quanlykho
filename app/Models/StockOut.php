<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class StockOut extends Model
{
    protected $fillable = ['code', 'warehouse_id', 'user_id', 'customer_name', 'total_amount', 'note', 'status', 'approved_by', 'approved_at'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
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

    // Tạo phiếu mới (pending - chưa cập nhật tồn kho)
    public static function createWithDetails(array $data, array $details): self
    {
        return DB::transaction(function () use ($data, $details) {
            $data['status'] = 'pending'; // Mặc định pending
            $stockOut = self::create($data);
            $totalAmount = 0;

            foreach ($details as $detail) {
                $stockOut->details()->create($detail);
                $totalAmount += $detail['quantity'] * $detail['unit_price'];
            }

            $stockOut->update(['total_amount' => $totalAmount]);
            return $stockOut;
        });
    }


    // Duyệt phiếu (Admin) - kiểm tra tồn kho và cập nhật
    public function approve(int $approverId): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return DB::transaction(function () use ($approverId) {
            // Kiểm tra tồn kho trước
            foreach ($this->details as $detail) {
                $currentStock = Inventory::getStock($detail->product_id, $this->warehouse_id);
                if ($currentStock < $detail->quantity) {
                    throw new \Exception("Sản phẩm '{$detail->product->name}' không đủ số lượng trong kho! (Cần: {$detail->quantity}, Có: {$currentStock})");
                }
            }

            // Trừ tồn kho
            foreach ($this->details as $detail) {
                Inventory::updateStock(
                    $detail->product_id,
                    $this->warehouse_id,
                    $detail->quantity,
                    false // decrease
                );
            }

            $this->update([
                'status' => 'completed',
                'approved_by' => $approverId,
                'approved_at' => now(),
            ]);

            return true;
        });
    }

    // Hủy phiếu (Admin) - hoàn trả tồn kho nếu đã completed
    public function cancel(int $approverId): bool
    {
        if ($this->status === 'cancelled') {
            return false;
        }

        return DB::transaction(function () use ($approverId) {
            // Nếu đã completed thì hoàn trả tồn kho
            if ($this->status === 'completed') {
                foreach ($this->details as $detail) {
                    Inventory::updateStock(
                        $detail->product_id,
                        $this->warehouse_id,
                        $detail->quantity,
                        true // increase (hoàn trả)
                    );
                }
            }

            $this->update([
                'status' => 'cancelled',
                'approved_by' => $approverId,
                'approved_at' => now(),
            ]);

            return true;
        });
    }

    // Cập nhật phiếu pending
    public function updateWithDetails(array $data, array $details): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return DB::transaction(function () use ($data, $details) {
            $this->update($data);
            $this->details()->delete();
            
            $totalAmount = 0;
            foreach ($details as $detail) {
                $this->details()->create($detail);
                $totalAmount += $detail['quantity'] * $detail['unit_price'];
            }

            $this->update(['total_amount' => $totalAmount]);
            return true;
        });
    }

    public static function getMonthlyStats($year = null)
    {
        $year = $year ?? date('Y');
        return self::selectRaw('EXTRACT(MONTH FROM created_at)::integer as month, COUNT(*) as count, SUM(total_amount) as total')
            ->whereYear('created_at', $year)
            ->where('status', 'completed')
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
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
