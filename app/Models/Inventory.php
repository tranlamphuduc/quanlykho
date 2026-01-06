<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $table = 'inventory';
    
    protected $fillable = ['product_id', 'warehouse_id', 'quantity'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public static function updateStock($productId, $warehouseId, $quantity, $isAdd = true): void
    {
        $inventory = self::firstOrCreate(
            ['product_id' => $productId, 'warehouse_id' => $warehouseId],
            ['quantity' => 0]
        );

        $inventory->quantity = $isAdd 
            ? $inventory->quantity + $quantity 
            : $inventory->quantity - $quantity;
        
        $inventory->save();
    }

    public static function getStock($productId, $warehouseId): int
    {
        return self::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->value('quantity') ?? 0;
    }

    public static function getLowStockProducts()
    {
        return self::with(['product.category', 'warehouse'])
            ->whereHas('product', function ($q) {
                $q->whereColumn('inventory.quantity', '<=', 'products.min_stock');
            })
            ->get();
    }

    public static function getTotalValue($warehouseId = null)
    {
        $query = self::join('products', 'inventory.product_id', '=', 'products.id')
            ->selectRaw('SUM(inventory.quantity * products.cost_price) as total');
        
        if ($warehouseId) {
            $query->where('inventory.warehouse_id', $warehouseId);
        }
        
        return $query->value('total') ?? 0;
    }
}
