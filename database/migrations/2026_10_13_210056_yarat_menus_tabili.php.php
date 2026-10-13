<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('location', 50)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('location');
            $table->index('is_active');
        });

        Schema::create('rj_menu_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('type', 30)->default('custom');
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('target', 10)->default('_self');
            $table->unsignedBigInteger('object_id')->nullable();
            $table->string('object_type', 50)->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('css_class')->nullable();
            $table->string('icon', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('menu_id')->references('id')->on('rj_menus')->cascadeOnDelete();
            $table->foreign('parent_id')->references('id')->on('rj_menu_items')->nullOnDelete();
            $table->index('menu_id');
            $table->index('parent_id');
            $table->index('sort_order');
            $table->index(['object_type', 'object_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_menu_items');
        Schema::dropIfExists('rj_menus');
    }
};