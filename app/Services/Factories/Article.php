<?php

namespace App\Services\Factories;

use App\Services\TDGs\Articles;
use App\Services\TDGs\WordArticle;
use App\Services\TDGs\Words;

class Article
{
    public function __construct(private Articles $articlesTdg, private WordArticle $wordArticleTdg, private Words $wordsTdg){}
    public function createByDB(int $articleId): \App\Entities\Article
    {
        $articleData = $this->articlesTdg->get($articleId);
        $wordsData = $this->wordArticleTdg->getWordIdsAndNumberOfOccurrencesByArticleId($articleId);
        $wordsIds = $wordsData->map(function ($item) {
            return $item->word_id;
        });
        $directlyWords = $this->wordsTdg->getWords($wordsIds)->map(function ($item) {
            return $item->word;
        });
        $numberOfOccurrences = $wordsData->map(function ($item) {
            return $item->number_of_occurrences;
        });

        $words = [];

        foreach ($directlyWords->combine($numberOfOccurrences) as $word => $number) {
            for ($i = 1; $i <= $number; $i++) {
                $words[] = $word;
            }
        }
        return new \App\Entities\Article($articleData->title, $articleData->content, collect($words));
    }
}
