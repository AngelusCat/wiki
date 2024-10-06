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
        $words = $this->wordsTdg->getWords($this->wordArticleTdg->getWordIdsByArticleId($articleId))->map(function ($item) {
            return $item->word;
        });
        return new \App\Entities\Article($articleData->title, $articleData->content, $words);
    }
}
