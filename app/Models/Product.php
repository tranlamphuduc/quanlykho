<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'code', 'barcode', 'name', 'category_id', 'supplier_id',
        'unit', 'cost_price', 'sell_price', 'min_stock', 'max_stock',
        'description', 'image'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public static function generateCode(): string
    {
        $lastProduct = self::where('code', 'like', 'SP%')->orderBy('code', 'desc')->first();
        $nextNum = $lastProduct ? (int)substr($lastProduct->code, 2) + 1 : 1;
        return 'SP' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
    }

    public function getStockInWarehouse($warehouseId): int
    {
        return $this->inventory()->where('warehouse_id', $warehouseId)->value('quantity') ?? 0;
    }
}
