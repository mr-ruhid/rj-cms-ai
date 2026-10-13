<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_tags', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->unsignedInteger('post_count')->default(0);
            $table->timestamps();

            $table->index('post_count');
        });

        Schema::create('rj_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tag_id');
            $table->string('locale', 10);
            $table->string('name');
            $table->timestamps();

            $table->foreign('tag_id')->references('id')->on('rj_tags')->cascadeOnDelete();
            $table->unique(['tag_id', 'locale']);
            $table->index('locale');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_tag_translations');
        Schema::dropIfExists('rj_tags');
    }
};