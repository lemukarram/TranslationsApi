<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('translation_groups');
            $table->string('key');
            $table->text('value');
            $table->foreignId('language_id')->constrained('languages');
            $table->json('tags')->nullable();
            $table->timestamps();
            
            $table->unique(['group_id', 'key', 'language_id']);
            $table->index(['key']);
            $table->index(['language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
