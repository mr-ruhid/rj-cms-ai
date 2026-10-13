<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rj_menu_items', function (Blueprint $table) {
            $table->json('translations')->nullable()->after('label');
        });
    }

    public function down(): void
    {
        Schema::table('rj_menu_items', function (Blueprint $table) {
            $table->dropColumn('translations');
        });
    }
};