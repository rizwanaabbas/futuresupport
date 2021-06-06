<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ChatsController as Chats;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
//Route::resource('chats', 'API\ChatsController');

//Route::middleware('auth:sanctum')->group( function () {
Route::resource('chats', 'API\ChatsController');
Route::post('chats/change-chat-status', [Chats::class, 'setChatStatus']);
Route::post('chats/search', [Chats::class, 'searchChats']);



//});

