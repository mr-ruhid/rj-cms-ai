<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('key');
            $table->string('type');
            $table->text('description')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('default_value')->nullable();
            $table->json('options')->nullable();
            $table->json('validation')->nullable();
            $table->json('settings')->nullable();
            $table->json('post_types')->nullable();
            $table->json('taxonomies')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_repeatable')->default(false);
            $table->string('location')->default('post');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status')->default('active');
            $table->unsignedBigInteger('author_id')->nullable();
            $table->timestamps();

            $table->index('slug');
            $table->index('key');
            $table->index('type');
            $table->index('location');
            $table->index('status');
        });

        Schema::create('rj_custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('field_id');
            $table->string('valuable_type');
            $table->unsignedBigInteger('valuable_id');
            $table->longText('value')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('field_id')->references('id')->on('rj_custom_fields')->onDelete('cascade');
            $table->index(['valuable_type', 'valuable_id']);
            $table->index('field_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_custom_field_values');
        Schema::dropIfExists('rj_custom_fields');
    }
};