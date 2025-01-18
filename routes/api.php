<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\CampaignController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

    // Customer API endpoints
    Route::apiResource('customers', CustomerController::class);
    Route::controller(CustomerController::class)->group(function () {
        Route::post('customers/bulk-delete', 'bulkDelete');
        Route::post('customers/import', 'import');
    });

    // Group API endpoints
    Route::apiResource('groups', GroupController::class);

    // Campaign API endpoints
    Route::apiResource('campaigns', CampaignController::class);
    Route::post('campaigns/{campaign}/execute', [CampaignController::class, 'execute']);

    // Message API endpoints
    Route::controller(MessageController::class)->group(function () {
        Route::get('messages', 'index');
        Route::post('messages/send', 'send');
    });

    Route::get('/customers', [CustomerController::class, 'apiIndex']);

    Route::post('/campaigns/{campaign}/duplicate', [CampaignController::class, 'duplicate']);
Route::post('/campaigns/{campaign}/resend', [CampaignController::class, 'resend']);

