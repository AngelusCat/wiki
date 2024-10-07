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
        Schema::create('word_article', function (Blueprint $table) {
            $table->foreignId("article_id")->references("id")->on("articles");
            $table->foreignId("word_id")->references("id")->on("words");
            $table->primary(["article_id", "word_id"]);
            $table->unsignedSmallInteger("number_of_occurrences");
            $table->engine('InnoDB');
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('word_article');
    }
};
