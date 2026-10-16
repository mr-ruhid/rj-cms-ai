<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rj_category_translations')) {
            return;
        }

        Schema::create('rj_category_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('locale', 10);
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->string('seo_title', 255)->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_image', 500)->nullable();
            $table->timestamps();

            $table->index(['category_id', 'locale']);
            $table->unique(['category_id', 'locale']);

            $table->foreign('category_id')
                ->references('id')
                ->on('rj_categories')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_category_translations');
    }
};