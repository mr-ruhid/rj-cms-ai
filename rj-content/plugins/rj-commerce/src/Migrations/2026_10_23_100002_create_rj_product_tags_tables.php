<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_product_tags', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('rj_product_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_tag_id')->constrained('rj_product_tags')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');
            $table->timestamps();

            $table->unique(['product_tag_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_product_tag_translations');
        Schema::dropIfExists('rj_product_tags');
    }
};
