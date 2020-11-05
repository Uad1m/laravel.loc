<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\OrganizationController;
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
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/
/*
Route::group(['middleware' => 'auth:sanctum'], function () {

Route::apiResource('user', UserController::class);

});
*/


Route::group(['middleware' => ['api']], function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});


Route::middleware(['auth:sanctum'])->group(function () {
		Route::apiResource('user', UserController::class)->except(['store']);
		Route::apiResource('organization', OrganizationController::class);
		Route::apiResource('vacancy', VacancyController::class);
		Route::post('vacancy-book', [VacancyController::class, 'book']);
		Route::post('vacancy-unbook', [VacancyController::class, 'unbook']);
		Route::get('stats/vacancy', [VacancyController::class, 'statsVacancy']);
		Route::get('stats/organization', [OrganizationController::class, 'statsOrganization']);
		Route::get('stats/user', [UserController::class, 'statsUser']);
});
/*
Route::apiResource('user', UserController::class)->except(['store']);
Route::apiResource('organization', OrganizationController::class);
Route::apiResource('vacancy', VacancyController::class);
*/
Route::fallback([AuthController::class, 'fallback']);