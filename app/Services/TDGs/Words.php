<?php

namespace App\Services\TDGs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Words
{
    private string $tableName = "words";

    public function massSave(Collection $words): Collection
    {
        $alreadyThere = DB::table($this->tableName)->whereIn('word', $words)->get();
        $alreadyThereWords = $alreadyThere->map(function ($item) {
            return $item->word;
        });
        $alreadyThereIds = $alreadyThere->map(function ($item) {
            return $item->id;
        });
        $notYet = $alreadyThereWords->diff($words);
        $forInsert = $notYet->map(function ($item) {
            return ["word" => $item];
        });
        DB::table($this->tableName)->insert($forInsert->toArray());
        $otherIds = DB::table($this->tableName)->whereIn('word', $notYet)->get();
        return $alreadyThereIds->merge($otherIds);
    }
}
