<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_widgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('class');
            $table->string('version')->default('1.0.0');
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('author_url')->nullable();
            $table->string('icon')->nullable();
            $table->string('category')->default('custom');
            $table->string('folder');
            $table->string('main_file');
            $table->string('status')->default('active');
            $table->json('schema')->nullable();
            $table->json('defaults')->nullable();
            $table->json('requires')->nullable();
            $table->json('metadata')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('category');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_widgets');
    }
};