<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('kyc_status')->default('none')->after('status'); // none, pending, approved, rejected
            $table->timestamp('kyc_verified_at')->nullable()->after('kyc_status');
        });

        Schema::create('rj_kyc_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('driver'); // manual, api (sumsub), plugin_name
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->string('document_type')->nullable(); // id_card, passport, selfie
            $table->string('document_front')->nullable();
            $table->string('document_back')->nullable();
            $table->string('selfie')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('metadata')->nullable(); // API cavabları və ya əlavə məlumatlar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_kyc_verifications');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kyc_status', 'kyc_verified_at']);
        });
    }
};
