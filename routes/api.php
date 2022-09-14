<?php

use App\Classes\Constants\RegularExpressions;
use App\Http\Controllers\Access\Api\AccessController;
use App\Http\Controllers\Auth\Api\AuthorizationController;
use App\Http\Controllers\Categories\Api\CategoriesController;
use App\Http\Controllers\Passwords\Api\PasswordsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("/invite/check/{code}", [AccessController::class, "check"]);

Route::group(["prefix" => "auth"], function (){
    Route::post("/login", [AuthorizationController::class, "login"]);
    Route::post("/register", [AuthorizationController::class, "register"]);
});

Route::group(["middleware" => "auth:sanctum"], function (){
    Route::group(["prefix" => "categories"], function (){
        Route::get("/all", [CategoriesController::class, "all"]);
    });

    Route::group(["prefix" => "passwords"], function (){
        Route::get("/get", [PasswordsController::class, "getByUser"]);
        Route::post("/set", [PasswordsController::class, "setForUser"]);
        Route::post("/update/{passwordId}", [PasswordsController::class, "updateForUser"])->where("passwordId", RegularExpressions::ONLY_NUMBERS);
        Route::delete("/delete/{passwordId}", [PasswordsController::class, "deleteByUser"])->where("passwordId", RegularExpressions::ONLY_NUMBERS);

        Route::post("/decrypt/{passwordId}", [PasswordsController::class, "decryptByUser"])->where("passwordId", RegularExpressions::ONLY_NUMBERS);
    });
});
