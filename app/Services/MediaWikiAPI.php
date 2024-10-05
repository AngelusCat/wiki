<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class MediaWikiAPI
{
    private string $endPoint = "https://ru.wikipedia.org/w/api.php";

    public function getPlainTextOfArticle(string $title): ?string
    {
        $response = Http::get($this->endPoint, [
            "action" => "query",
            "prop" => "extracts",
            "exlimit" => 1,
            "titles" => $title,
            "explaintext" => 1,
            "format" => "json"
        ]);

        $result = collect(json_decode($response->body(), JSON_OBJECT_AS_ARRAY))->dot();

        return ($this->responseContainsPlainText($result) === false) ? null : $this->getPlainTextFromResponse($result);
    }

    private function responseContainsPlainText(Collection $response): bool
    {
        return $response->containsStrict(function ($value, $key) {
            return str_ends_with($key, "extract");
        });
    }

    private function getPlainTextFromResponse(Collection $response): ?string
    {
        return $response->first(function ($value, $key) {
            return str_ends_with($key, "extract") && !empty($value);
        });
    }
}
