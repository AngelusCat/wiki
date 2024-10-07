<?php

namespace App\Http\Controllers;

use App\Entities\Article;
use App\Services\MediaWikiAPI;
use App\Services\TDGs\Articles;
use App\Services\TDGs\WordArticle;
use App\Services\TDGs\Words;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WikiParserController extends Controller
{
    public function __construct(private MediaWikiAPI $api, private Articles $articlesTdg, private Words $wordsTdg, private WordArticle $wordArticleTdg, private \App\Services\Factories\Article $factory){}

    /**
     * @throws ValidationException
     */
    public function import(Request $request): JsonResponse
    {
        $start = microtime(true);
        $this->validate($request, [
            "title" => "required|string",
        ]);
        $title = $request->input('title');

        if ($this->articlesTdg->articleHasAlreadyBeenCopied($title)) {
            return response()->json([]);
        }

        $content = $this->api->getPlainTextOfArticle($title);
        if ($content === null) {
            return response()->json([]);
        }
        $article = new Article($title, $content);
        try {
            DB::beginTransaction();
            $article->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $processingTime = round(microtime(true) - $start, 4);

        return response()->json([
            "title" => $article->getTitle(),
            "link" => $article->getLink(),
            "size" => $article->getSize(),
            "wordCount" => $article->getWordCount(),
            "processingTime" => $processingTime
        ]);
    }

    public function getArticlesByIds(int $startId, int $endId): JsonResponse
    {
        $articlesFromDB = $this->articlesTdg->getArticles($startId, $endId);
        $articles = [];
        foreach ($articlesFromDB as $article) {
            $articles[] = $this->factory->createByDB($article->id);
        }
        $response = [];
        foreach ($articles as $article) {
            $response[] = [
                "title" => $article->getTitle(),
                "link" => $article->getLink(),
                "size" => $article->getSize(),
                "wordCount" => $article->getWordCount()
            ];
        }
        return response()->json($response);
    }

    /**
     * @throws ValidationException
     */
    public function search(Request $request): JsonResponse
    {
        $this->validate($request, [
            "keyword" => "required|string",
        ]);
        $keyWord = strtr($request->input('keyword'), ["Ё" => "Е", "ё" => "е"]);
        $articleIds = $this->wordArticleTdg->getArticleIdsByWordId($this->wordsTdg->getIdByWord($keyWord));
        $articles = [];
        $response = [];
        foreach ($articleIds as $articleId) {
            $articles[] = $this->factory->createByDB($articleId);
        }
        foreach ($articles as $article) {
            $response[] = [
                "title" => $article->getTitle(),
                "numberOfOccurrences" => $article->getNumberOfOccurrencesOfWord($keyWord),
                "content" => $article->getContent()
            ];
        }
        return response()->json($response);
    }
}
