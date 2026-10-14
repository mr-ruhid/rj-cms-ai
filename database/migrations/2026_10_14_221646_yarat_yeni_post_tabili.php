<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('type')->default('post');
            $table->string('status')->default('draft');
            $table->boolean('is_sticky')->default(false);
            $table->boolean('allow_comments')->default(true);
            $table->integer('view_count')->default(0);
            $table->unsignedBigInteger('author_id')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->json('settings')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
            $table->index('published_at');
            $table->index('author_id');
        });

        Schema::create('rj_post_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->string('locale', 10);
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('rj_posts')->onDelete('cascade');
            $table->unique(['post_id', 'locale']);
        });

        Schema::create('rj_post_category', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('category_id');

            $table->primary(['post_id', 'category_id']);
            $table->foreign('post_id')->references('id')->on('rj_posts')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('rj_categories')->onDelete('cascade');
        });

        Schema::create('rj_post_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('tag_id');

            $table->primary(['post_id', 'tag_id']);
            $table->foreign('post_id')->references('id')->on('rj_posts')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('rj_tags')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_post_tag');
        Schema::dropIfExists('rj_post_category');
        Schema::dropIfExists('rj_post_translations');
        Schema::dropIfExists('rj_posts');
    }
};