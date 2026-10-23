<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('type')->default('select');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('rj_product_attribute_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attribute_id')->constrained('rj_product_attributes')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');
            $table->timestamps();

            $table->unique(['product_attribute_id', 'locale'], 'rj_attr_trans_unique');
        });

        Schema::create('rj_product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attribute_id')->constrained('rj_product_attributes')->cascadeOnDelete();
            $table->string('slug');
            $table->string('color_code')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['product_attribute_id', 'slug'], 'rj_attr_val_unique');
        });

        Schema::create('rj_product_attribute_value_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attribute_value_id')->constrained('rj_product_attribute_values')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');
            $table->timestamps();

            $table->unique(['product_attribute_value_id', 'locale'], 'rj_attr_val_trans_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_product_attribute_value_translations');
        Schema::dropIfExists('rj_product_attribute_values');
        Schema::dropIfExists('rj_product_attribute_translations');
        Schema::dropIfExists('rj_product_attributes');
    }
};
