<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('rj_product_tag_pivot');
        Schema::dropIfExists('rj_product_translations');
        Schema::dropIfExists('rj_product_variations');
        Schema::dropIfExists('rj_products');

        Schema::create('rj_products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('type')->default('physical');
            $table->string('sku')->nullable()->unique();
            $table->string('barcode')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('compare_price', 15, 2)->nullable();

            $table->boolean('manage_stock')->default(true);
            $table->integer('stock_quantity')->default(0);
            $table->string('stock_status')->default('in_stock');

            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();

            $table->json('downloadable_files')->nullable();
            $table->integer('download_limit')->nullable();
            $table->integer('download_expiry')->nullable();
            $table->string('external_url')->nullable();

            $table->foreignId('category_id')->nullable()->constrained('rj_product_categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('rj_product_brands')->nullOnDelete();
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::create('rj_product_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('rj_products')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'locale']);
        });

        Schema::create('rj_product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('rj_products')->cascadeOnDelete();
            $table->string('sku')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('compare_price', 15, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->string('image')->nullable();
            $table->json('attributes');
            $table->timestamps();
        });

        Schema::create('rj_product_tag_pivot', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('rj_products')->cascadeOnDelete();
            $table->foreignId('product_tag_id')->constrained('rj_product_tags')->cascadeOnDelete();
            $table->primary(['product_id', 'product_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_product_tag_pivot');
        Schema::dropIfExists('rj_product_variations');
        Schema::dropIfExists('rj_product_translations');
        Schema::dropIfExists('rj_products');
    }
};
