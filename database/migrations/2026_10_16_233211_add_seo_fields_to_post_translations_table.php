<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rj_post_translations', function (Blueprint $table) {
            $table->string('seo_title', 255)->nullable()->after('excerpt');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('seo_image', 500)->nullable()->after('seo_description');
            $table->string('focus_keyword', 100)->nullable()->after('seo_image');
            $table->string('canonical_url', 500)->nullable()->after('focus_keyword');

            $table->string('og_title', 255)->nullable()->after('canonical_url');
            $table->text('og_description')->nullable()->after('og_title');
            $table->string('og_image', 500)->nullable()->after('og_description');

            $table->string('twitter_card', 30)->nullable()->default('summary_large_image')->after('og_image');
            $table->string('twitter_title', 255)->nullable()->after('twitter_card');
            $table->text('twitter_description')->nullable()->after('twitter_title');
            $table->string('twitter_image', 500)->nullable()->after('twitter_description');

            $table->string('robots', 50)->nullable()->default('index,follow')->after('twitter_image');
            $table->string('schema_type', 50)->nullable()->default('Article')->after('robots');
        });
    }

    public function down(): void
    {
        Schema::table('rj_post_translations', function (Blueprint $table) {
            $table->dropColumn([
                'seo_title',
                'seo_description',
                'seo_image',
                'focus_keyword',
                'canonical_url',
                'og_title',
                'og_description',
                'og_image',
                'twitter_card',
                'twitter_title',
                'twitter_description',
                'twitter_image',
                'robots',
                'schema_type',
            ]);
        });
    }
};