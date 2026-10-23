<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_product_brands', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('rj_product_brand_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_brand_id')->constrained('rj_product_brands')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();

            $table->unique(['product_brand_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_product_brand_translations');
        Schema::dropIfExists('rj_product_brands');
    }
};
