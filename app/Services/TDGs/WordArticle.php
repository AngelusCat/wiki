<?php

namespace App\Services\TDGs;

use App\Entities\Article;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WordArticle
{
    private string $tableName = "word_article";

    public function save(Article $article, Collection $wordsIds): void
    {
        $forInsert = $wordsIds->map(function ($item) use ($article) {
            return ["article_id" => $article->getId(), "word_id" => $item->id, "number_of_occurrences" => $article->getNumberOfOccurrencesOfWord($item->word)];
        });
        DB::table($this->tableName)->insert($forInsert->toArray());
    }
}
