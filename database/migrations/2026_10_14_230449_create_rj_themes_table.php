<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('version')->default('1.0.0');
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('author_url')->nullable();
            $table->string('preview_image')->nullable();
            $table->string('folder');
            $table->string('status')->default('inactive');
            $table->boolean('is_child')->default(false);
            $table->string('parent_slug')->nullable();
            $table->json('supports')->nullable();
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('slug');
        });

        Schema::create('rj_theme_customizations', function (Blueprint $table) {
            $table->id();
            $table->string('theme_slug');
            $table->string('section');
            $table->string('key');
            $table->longText('value')->nullable();
            $table->timestamps();

            $table->unique(['theme_slug', 'section', 'key']);
            $table->index('theme_slug');
            $table->index(['theme_slug', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_theme_customizations');
        Schema::dropIfExists('rj_themes');
    }
};