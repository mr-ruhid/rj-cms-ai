<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('rj_products')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('rating')->default(5);
            $table->text('comment')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('rj_shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('regions')->nullable();
            $table->timestamps();
        });

        Schema::create('rj_shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained('rj_shipping_zones')->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('flat_rate');
            $table->decimal('cost', 15, 2)->default(0);
            $table->decimal('min_amount', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_shipping_methods');
        Schema::dropIfExists('rj_shipping_zones');
        Schema::dropIfExists('rj_product_reviews');
    }
};
