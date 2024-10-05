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
        if ($alreadyThere->isEmpty()) {
            $forInsert = $words->map(function ($item) {
                return ["word" => $item];
            });
            DB::table($this->tableName)->insert($forInsert->toArray());
            return DB::table($this->tableName)->whereIn('word', $words)->get();
        }
        $alreadyThereWords = $alreadyThere->map(function ($item) {
            return $item->word;
        });
        $notYet = $alreadyThereWords->diff($words);
        $forInsert = $notYet->map(function ($item) {
            return ["word" => $item];
        });
        DB::table($this->tableName)->insert($forInsert->toArray());
        $other = DB::table($this->tableName)->whereIn('word', $notYet)->get();
        return $alreadyThere->merge($other);
    }
}
