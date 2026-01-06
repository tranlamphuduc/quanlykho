<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOutDetail extends Model
{
    protected $fillable = [
        'stock_out_id', 'product_id', 'quantity', 'unit_price', 'serial_number'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function stockOut(): BelongsTo
    {
        return $this->belongsTo(StockOut::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
