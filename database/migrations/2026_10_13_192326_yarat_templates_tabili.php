<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type', 20);
            $table->longText('content')->nullable();
            $table->string('status', 20)->default('draft');
            $table->boolean('is_default')->default(false);
            $table->json('conditions')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('users')->nullOnDelete();
            $table->index('type');
            $table->index('status');
            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_templates');
    }
};