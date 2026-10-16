<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rj_posts', function (Blueprint $table) {
            $table->json('blocks')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('rj_posts', function (Blueprint $table) {
            $table->dropColumn('blocks');
        });
    }
};
