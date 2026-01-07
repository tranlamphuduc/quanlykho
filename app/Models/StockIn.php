<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class StockIn extends Model
{
    protected $fillable = ['code', 'warehouse_id', 'supplier_id', 'user_id', 'total_amount', 'note', 'status', 'approved_by', 'approved_at'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
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

    // Tạo phiếu mới (pending - chưa cập nhật tồn kho)
    public static function createWithDetails(array $data, array $details): self
    {
        return DB::transaction(function () use ($data, $details) {
            $data['status'] = 'pending'; // Mặc định pending
            $stockIn = self::create($data);
            $totalAmount = 0;

            foreach ($details as $detail) {
                $stockIn->details()->create($detail);
                $totalAmount += $detail['quantity'] * $detail['unit_price'];
            }

            $stockIn->update(['total_amount' => $totalAmount]);
            return $stockIn;
        });
    }

    // Duyệt phiếu (Admin) - cập nhật tồn kho
    public function approve(int $approverId): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return DB::transaction(function () use ($approverId) {
            // Cập nhật tồn kho
            foreach ($this->details as $detail) {
                Inventory::updateStock(
                    $detail->product_id,
                    $this->warehouse_id,
                    $detail->quantity,
                    true // increase
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
                        false // decrease
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
}
