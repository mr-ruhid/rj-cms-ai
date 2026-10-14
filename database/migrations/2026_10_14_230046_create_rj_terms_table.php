<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_terms', function (Blueprint $table) {
            $table->id();
            $table->string('taxonomy');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->integer('post_count')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['taxonomy', 'slug']);
            $table->index('taxonomy');
            $table->index('parent_id');
            $table->index('is_active');
            $table->index('sort_order');
        });

        Schema::create('rj_term_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('term_id');
            $table->string('locale', 10);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();

            $table->foreign('term_id')->references('id')->on('rj_terms')->onDelete('cascade');
            $table->unique(['term_id', 'locale']);
        });

        Schema::create('rj_post_term', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('term_id');

            $table->primary(['post_id', 'term_id']);
            $table->foreign('post_id')->references('id')->on('rj_posts')->onDelete('cascade');
            $table->foreign('term_id')->references('id')->on('rj_terms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_post_term');
        Schema::dropIfExists('rj_term_translations');
        Schema::dropIfExists('rj_terms');
    }
};