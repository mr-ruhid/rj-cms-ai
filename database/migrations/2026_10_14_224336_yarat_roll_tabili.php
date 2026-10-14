<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->integer('level')->default(0);
            $table->timestamps();

            $table->index('slug');
        });

        Schema::create('rj_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('group')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('slug');
            $table->index('group');
        });

        Schema::create('rj_role_permission', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');

            $table->primary(['role_id', 'permission_id']);
            $table->foreign('role_id')->references('id')->on('rj_roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('rj_permissions')->onDelete('cascade');
        });

        Schema::create('rj_user_role', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');

            $table->primary(['user_id', 'role_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('rj_roles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_user_role');
        Schema::dropIfExists('rj_role_permission');
        Schema::dropIfExists('rj_permissions');
        Schema::dropIfExists('rj_roles');
    }
};