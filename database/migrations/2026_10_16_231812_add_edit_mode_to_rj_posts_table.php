<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rj_posts', function (Blueprint $table) {
            $table->string('edit_mode', 20)->default('classic')->after('blocks');
            $table->string('theme_template', 100)->nullable()->after('edit_mode');
        });
    }

    public function down(): void
    {
        Schema::table('rj_posts', function (Blueprint $table) {
            $table->dropColumn(['edit_mode', 'theme_template']);
        });
    }
};