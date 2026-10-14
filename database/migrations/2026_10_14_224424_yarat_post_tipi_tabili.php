<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_post_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('singular_name');
            $table->string('plural_name');
            $table->string('icon')->nullable();
            $table->string('menu_position')->default('25');
            $table->boolean('is_public')->default(true);
            $table->boolean('has_archive')->default(true);
            $table->boolean('is_hierarchical')->default(false);
            $table->boolean('supports_comments')->default(true);
            $table->boolean('supports_builder')->default(true);
            $table->boolean('supports_thumbnail')->default(true);
            $table->json('supports')->nullable();
            $table->json('labels')->nullable();
            $table->json('settings')->nullable();
            $table->string('status')->default('active');
            $table->unsignedBigInteger('author_id')->nullable();
            $table->timestamps();

            $table->index('slug');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_post_types');
    }
};