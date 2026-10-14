<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_custom_icons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('source')->default('upload');
            $table->string('library')->nullable();
            $table->longText('svg')->nullable();
            $table->string('path')->nullable();
            $table->string('status')->default('active');
            $table->unsignedBigInteger('author_id')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('library');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_custom_icons');
    }
};