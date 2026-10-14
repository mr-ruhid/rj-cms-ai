<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_form_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id');
            $table->string('name');
            $table->string('label');
            $table->string('type');
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->string('default_value')->nullable();
            $table->json('options')->nullable();
            $table->json('validation')->nullable();
            $table->boolean('required')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('form_id')->references('id')->on('rj_forms')->onDelete('cascade');
            $table->index('form_id');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_form_fields');
    }
};