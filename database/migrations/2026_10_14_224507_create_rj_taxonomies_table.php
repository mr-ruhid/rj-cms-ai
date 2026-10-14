<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_taxonomies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('singular_name');
            $table->string('plural_name');
            $table->text('description')->nullable();
            $table->boolean('is_hierarchical')->default(false);
            $table->boolean('is_public')->default(true);
            $table->boolean('show_in_menu')->default(true);
            $table->json('post_types')->nullable();
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
        Schema::dropIfExists('rj_taxonomies');
    }
};