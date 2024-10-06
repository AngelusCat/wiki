<?php

namespace App\Http\Controllers;

use App\Entities\Article;
use App\Services\MediaWikiAPI;
use App\Services\TDGs\WordArticle;
use App\Services\TDGs\Words;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WikiParserController extends Controller
{
    public function __construct(private MediaWikiAPI $api, private Words $wordsTdg, private WordArticle $wordArticleTdg, private \App\Services\Factories\Article $factory){}
    public function import(Request $request)
    {
        //validation
        $start = microtime(true);
        $title = $request->input('title');
        $content = $this->api->getPlainTextOfArticle($title);
        if ($content === null) {
            //
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

    public function search(Request $request)
    {
        //validation
        $keyWord = $request->input('keyword');
        $articleIds = $this->wordArticleTdg->getArticleIdsByWordId($this->wordsTdg->getIdByWord($keyWord));
        foreach ($articleIds as $articleId) {
            $articles[] = $this->factory->createByDB($articleId);
        }
    }
}
