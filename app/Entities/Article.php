<?php

namespace App\Entities;

use App\Services\TDGs\Words;
use App\Services\WordsAtomsTDG;
use Illuminate\Support\Collection;

class Article
{
    private ?int $id;
    private string $title;
    private Collection $words;
    private string $content;
    private \App\Services\TDGs\Articles $articlesTdg;
    private Words $wordsTdg;

    public function __construct(string $title, string $content, Collection $words = new Collection())
    {
        $this->articlesTdg = app(\App\Services\TDGs\Articles::class);
        $this->wordsTdg = app(\App\Services\TDGs\Words::class);
        $this->title = $title;
        $this->content = $this->removeAccents($content);
        $this->words = ($words->isEmpty()) ? $this->parseContentIntoWords() : $words;
    }

    private function removeAccents(string $content): string
    {
        $replacementArray = [
            "А́" => "А",
            "а́" => "а",
            "Е́" => "Е",
            "е́" => "е",
            "И́" => "И",
            "и́" => "и",
            "О́" => "О",
            "о́" => "о",
            "У́" => "У",
            "у́" => "у",
            "Ы́" => "Ы",
            "ы́" => "ы",
            "Э́" => "Э",
            "э́" => "э",
            "Ю́" => "Ю",
            "ю́" => "ю",
            "Я́" => "Я",
            "я́" => "я"
        ];
        return strtr($content, $replacementArray);
    }

    public function getLink(): string
    {
        return "https://ru.wikipedia.org/wiki/" . $this->title;
    }

    public function getWordCount(): int
    {
        return $this->words->count();
    }

    public function getSize(): int
    {
        return mb_strlen($this->content, 'UTF-8');
    }

    private function parseContentIntoWords(): Collection
    {
        return collect(preg_split('/[^а-яёА-ЯЁ0-9a-zA-Z]+/u', mb_strtolower($this->content), -1, PREG_SPLIT_NO_EMPTY));
    }

    private function getUniqueWords(): Collection
    {
        return $this->words->uniqueStrict();
    }

    public function save(): void
    {
        $this->id = $this->articlesTdg->save($this->title, $this->content);
        $wordsIds = $this->wordsTdg->massSave($this->getUniqueWords());
        dump($wordsIds);
    }
}
