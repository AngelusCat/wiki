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

    public function get(int $id)
    {
        return DB::table($this->tableName)->where("id", $id)->first();
    }

    public function getArticles(int $startId, int $endId): Collection
    {
        return DB::table($this->tableName)->whereBetween("id", [$startId, $endId])->get();
    }
}
