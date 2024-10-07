<?php

namespace App\Services\TDGs;

use App\Entities\Article;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WordArticle
{
    private string $tableName = "word_article";

    public function massSave(Article $article, Collection $wordsIds): void
    {
        $forInsert = $wordsIds->map(function ($item) use ($article) {
            return ["article_id" => $article->getId(), "word_id" => $item->id, "number_of_occurrences" => $article->getNumberOfOccurrencesOfWord($item->word)];
        });
        DB::table($this->tableName)->insert($forInsert->toArray());
    }

    public function getArticleIdsByWordId(int $wordId): Collection
    {
        return DB::table($this->tableName)->where("word_id", "=", $wordId)->orderByDesc("number_of_occurrences")->pluck("article_id");
    }

    public function getWordIdsAndNumberOfOccurrencesByArticleId(int $articleId): Collection
    {
        return DB::table($this->tableName)->where("article_id", $articleId)->get(["word_id", "number_of_occurrences"]);
    }
}
