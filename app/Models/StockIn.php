<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class StockIn extends Model
{
    protected $fillable = ['code', 'warehouse_id', 'supplier_id', 'user_id', 'total_amount', 'note', 'status'];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(StockInDetail::class);
    }

    public static function generateCode(): string
    {
        $prefix = 'PN' . date('Ymd');
        $last = self::where('code', 'like', $prefix . '%')->orderBy('code', 'desc')->first();
        $nextNum = $last ? (int)substr($last->code, -4) + 1 : 1;
        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    public static function createWithDetails(array $data, array $details): self
    {
        return DB::transaction(function () use ($data, $details) {
            $stockIn = self::create($data);
            $totalAmount = 0;

            foreach ($details as $detail) {
                $stockIn->details()->create($detail);
                $totalAmount += $detail['quantity'] * $detail['unit_price'];
                
                Inventory::updateStock(
                    $detail['product_id'],
                    $data['warehouse_id'],
                    $detail['quantity'],
                    true
                );
            }

            $stockIn->update(['total_amount' => $totalAmount]);
            return $stockIn;
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
}
