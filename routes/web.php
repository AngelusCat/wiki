<?php

use App\Entities\Article;
use App\Http\Controllers\WikiParserController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $csrfToken = csrf_token();
    return view('wiki')->with('csrfToken', $csrfToken);
});

Route::post('/import', [WikiParserController::class, 'import']);

Route::get('/articles/{start}/{end}', function (int $start, int $end) {
    /**
     * @var \App\Services\TDGs\Articles $tdg
     */
    $tdg = app(\App\Services\TDGs\Articles::class);
    /**
     * @var \App\Services\Factories\Article $f
     */
    $f = app(\App\Services\Factories\Article::class);
    $articles = $tdg->getArticles($start, $end);

    $articles1 = [];

    foreach ($articles as $article) {
        $articles1[] = $f->createByDB($article->id);
    }

    $response = [];

    foreach ($articles1 as $article) {
        $response[] = [
            "title" => $article->getTitle(),
            "link" => $article->getLink(),
            "size" => $article->getSize(),
            "wordCount" => $article->getWordCount()
        ];
    }
    return response()->json($response);
});
