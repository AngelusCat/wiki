<?php

use App\Entities\Article;
use App\Http\Controllers\WikiParserController;
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
Route::post('/search', [WikiParserController::class, 'search']);

Route::get('/articles/{start}/{end}', [WikiParserController::class, 'getArticlesByIds']);
