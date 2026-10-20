<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rj_form_fields', function (Blueprint $table) {
            if (!Schema::hasColumn('rj_form_fields', 'is_required')) {
                $table->boolean('is_required')->default(false)->after('placeholder');
            }
            if (!Schema::hasColumn('rj_form_fields', 'options')) {
                $table->json('options')->nullable()->after('is_required');
            }
            if (!Schema::hasColumn('rj_form_fields', 'settings')) {
                $table->json('settings')->nullable()->after('options');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rj_form_fields', function (Blueprint $table) {
            if (Schema::hasColumn('rj_form_fields', 'is_required')) {
                $table->dropColumn('is_required');
            }
            if (Schema::hasColumn('rj_form_fields', 'options')) {
                $table->dropColumn('options');
            }
            if (Schema::hasColumn('rj_form_fields', 'settings')) {
                $table->dropColumn('settings');
            }
        });
    }
};