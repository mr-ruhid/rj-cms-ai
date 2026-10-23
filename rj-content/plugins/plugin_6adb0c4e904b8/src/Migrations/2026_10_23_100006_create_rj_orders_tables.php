<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('shipping_total', 15, 2)->default(0);
            $table->decimal('discount_total', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('payment_method')->nullable();
            $table->string('shipping_method')->nullable();
            $table->json('billing_address')->nullable();
            $table->json('shipping_address')->nullable();
            $table->text('customer_note')->nullable();
            $table->timestamps();
        });

        Schema::create('rj_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('rj_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('rj_products')->nullOnDelete();
            $table->foreignId('variation_id')->nullable()->constrained('rj_product_variations')->nullOnDelete();
            $table->string('product_name');
            $table->string('variation_name')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('total', 15, 2)->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_order_items');
        Schema::dropIfExists('rj_orders');
    }
};
