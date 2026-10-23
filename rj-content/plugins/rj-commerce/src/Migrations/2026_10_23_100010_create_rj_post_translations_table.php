<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rj_post_translations')) {
            Schema::create('rj_post_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained('rj_posts')->cascadeOnDelete();
                $table->string('locale', 10);
                $table->string('title')->nullable();
                $table->longText('content')->nullable();
                $table->text('excerpt')->nullable();

                $table->string('seo_title')->nullable();
                $table->text('seo_description')->nullable();
                $table->string('seo_image')->nullable();
                $table->string('focus_keyword')->nullable();
                $table->string('canonical_url')->nullable();

                $table->string('og_title')->nullable();
                $table->text('og_description')->nullable();
                $table->string('og_image')->nullable();

                $table->string('twitter_card')->nullable();
                $table->string('twitter_title')->nullable();
                $table->text('twitter_description')->nullable();
                $table->string('twitter_image')->nullable();

                $table->string('robots')->nullable();
                $table->string('schema_type')->nullable();

                $table->timestamps();

                $table->unique(['post_id', 'locale']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_post_translations');
    }
};
