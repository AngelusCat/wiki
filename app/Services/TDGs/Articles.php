<?php

namespace App\Services\TDGs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Articles
{
    private string $tableName = "articles";
    public function save(string $title, string $content): int
    {
        return DB::table($this->tableName)->insertGetId([
            "title" => $title,
            "content" => $content
        ]);
    }

    /**
     * @param int $id id статьи из БД.
     * @return object|null stdClass с данными для Article.
     */
    public function get(int $id): ?object
    {
        return DB::table($this->tableName)->where("id", $id)->first();
    }

    /**
     * Возвращает коллекцию stdClass с данными для Article, где article.id находится в промежутке между startId и endId.
     * @param int $startId
     * @param int $endId
     * @return Collection
     */

    public function getArticles(int $startId, int $endId): Collection
    {
        return DB::table($this->tableName)->whereBetween("id", [$startId, $endId])->get();
    }

    public function articleHasAlreadyBeenCopied(string $title): bool
    {
        return DB::table($this->tableName)->where("title", "=", $title)->exists();
    }
}
