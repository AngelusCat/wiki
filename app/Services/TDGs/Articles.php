<?php

namespace App\Services\TDGs;

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
}
