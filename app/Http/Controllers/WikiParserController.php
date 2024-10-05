<?php

namespace App\Http\Controllers;

use App\Entities\Article;
use App\Services\MediaWikiAPI;
use Illuminate\Http\Request;

class WikiParserController extends Controller
{
    public function __construct(private MediaWikiAPI $api){}
    public function import(Request $request)
    {
        //validation
        $title = $request->input('title');
        $content = $this->api->getPlainTextOfArticle($title);
        if ($content === null) {
            //redirect
        }
        $article = new Article($title, $content);
        $article->save();
    }
}
