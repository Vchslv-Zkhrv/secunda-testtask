<?php

use App\DTOs\DTO;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\BusinessActivityController;
use App\Http\Controllers\CompanyController;
use App\Http\Middleware\ApiKeyAuthMiddleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::prefix('/api')
    ->name('api_')
    ->middleware(ApiKeyAuthMiddleware::class)
    ->withoutMiddleware(VerifyCsrfToken::class)
    ->group(function () {

    Route::pattern('id', DTO::UUID_REGEX);

    Route::controller(BuildingController::class)->prefix('/building')->name('building_')->group(function () {
        Route::get('', 'index')->name('list_all');
        Route::post('', 'store')->name('create');
        Route::get('/{id}', 'show')->name('show_one');
        Route::put('/{id}', 'update')->name('update');
        Route::get('/{id}/companies', 'listCompanies')->name('list_companies');
        Route::delete('/{id}', 'destroy')->name('delete');

        Route::prefix('/geo')->name('geo_')->group(function () {
            Route::post('/rect', 'findInRect')->name('find_in_rect');
            Route::post('/circle', 'findInCircle')->name('find_in_circle');
        });
    });

    Route::controller(BusinessActivityController::class)->prefix('/business-activity')->name('business_activity_')->group(function () {
        Route::get('', 'index')->name('show_full_tree');
        Route::post('', 'store')->name('add_one');
        Route::get('/{id}', 'show')->name('show_subtree');
        Route::get('/{id}/companies', 'listCompanies')->name('list_companies');
        Route::put('/{id}', 'update')->name('update');
        Route::patch('/{id}', 'moveTree')->name('move_tree');
        Route::delete('/{id}', 'destroy')->name('delete_subtree');
    });

    Route::controller(CompanyController::class)->prefix('/company')->name('company')->group(function () {
        Route::get('', 'index')->name('list_all');
        Route::post('/search', 'search')->name('search');
        Route::post('', 'store')->name('create');
        Route::get('/{id}', 'show')->name('show_one');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('delete');
    });

});
