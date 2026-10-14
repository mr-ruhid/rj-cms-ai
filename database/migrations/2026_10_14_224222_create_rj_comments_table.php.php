<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('author_name');
            $table->string('author_email');
            $table->string('author_url')->nullable();
            $table->string('author_ip', 45)->nullable();
            $table->text('content');
            $table->string('status')->default('pending');
            $table->integer('like_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('rj_posts')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('rj_comments')->onDelete('cascade');
            $table->index('post_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_comments');
    }
};