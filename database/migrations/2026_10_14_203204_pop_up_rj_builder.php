<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rj_templates', function (Blueprint $table) {
            $table->json('settings')->nullable()->after('conditions');
        });
    }

    public function down(): void
    {
        Schema::table('rj_templates', function (Blueprint $table) {
            $table->dropColumn('settings');
        });
    }
};