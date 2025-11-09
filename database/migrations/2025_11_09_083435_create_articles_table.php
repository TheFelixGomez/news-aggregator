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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('sources')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable(); // Store full content if available
            $table->string('url')->unique(); // URL to the original article
            $table->string('image_url')->nullable(); // Main image URL
            $table->timestamp('published_at');

            // For tracking and de-duplication during scraping
            $table->string('api_source'); // e.g., 'newsapi', 'guardian', 'nyt'
            $table->string('original_id')->nullable(); // The ID from the source API

            $table->timestamps();

            $table->index('published_at');
            $table->unique(['api_source', 'original_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
