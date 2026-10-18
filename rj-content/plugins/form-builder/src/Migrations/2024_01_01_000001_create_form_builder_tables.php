<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rj_forms')) {
            Schema::create('rj_forms', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->json('settings')->nullable();
                $table->string('status', 20)->default('active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('rj_form_fields')) {
            Schema::create('rj_form_fields', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('form_id');
                $table->string('type', 50);
                $table->string('name', 100);
                $table->string('label', 255);
                $table->string('placeholder', 255)->nullable();
                $table->text('help_text')->nullable();
                $table->boolean('is_required')->default(false);
                $table->json('options')->nullable();
                $table->integer('sort_order')->default(0);
                $table->json('settings')->nullable();
                $table->timestamps();

                $table->foreign('form_id')->references('id')->on('rj_forms')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('rj_form_submissions')) {
            Schema::create('rj_form_submissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('form_id');
                $table->json('data');
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 500)->nullable();
                $table->string('status', 20)->default('new');
                $table->timestamps();

                $table->foreign('form_id')->references('id')->on('rj_forms')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_form_submissions');
        Schema::dropIfExists('rj_form_fields');
        Schema::dropIfExists('rj_forms');
    }
};
