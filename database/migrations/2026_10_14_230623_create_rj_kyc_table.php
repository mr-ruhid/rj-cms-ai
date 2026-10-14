<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rj_kyc_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('pending');
            $table->string('document_type');
            $table->string('document_number')->nullable();
            $table->string('document_front')->nullable();
            $table->string('document_back')->nullable();
            $table->string('selfie_image')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('nationality', 10)->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country', 10)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
            $table->index('status');
            $table->index('reviewed_by');
        });

        Schema::create('rj_kyc_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('verification_id');
            $table->string('type');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->timestamps();

            $table->foreign('verification_id')->references('id')->on('rj_kyc_verifications')->onDelete('cascade');
            $table->index('verification_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_kyc_documents');
        Schema::dropIfExists('rj_kyc_verifications');
    }
};