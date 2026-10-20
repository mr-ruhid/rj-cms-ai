<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rj_knowledge', function (Blueprint $table) {
            $table->dropColumn(['title', 'content']);
        });

        Schema::create('rj_knowledge_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_id')->constrained('rj_knowledge')->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->timestamps();

            $table->unique(['knowledge_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rj_knowledge_translations');

        Schema::table('rj_knowledge', function (Blueprint $table) {
            $table->string('title')->after('slug');
            $table->longText('content')->nullable()->after('category');
        });
    }
};