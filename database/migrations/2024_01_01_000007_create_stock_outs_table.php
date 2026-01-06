<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->foreignId('warehouse_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('customer_name', 150)->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->timestamps();
        });

        Schema::create('stock_out_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_out_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->string('serial_number', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_out_details');
        Schema::dropIfExists('stock_outs');
    }
};
