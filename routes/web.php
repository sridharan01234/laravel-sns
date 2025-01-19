<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
require __DIR__.'/auth.php';

// Protected routes
// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

// Group routes
Route::resource('groups', GroupController::class);

// Campaign routes
Route::resource('campaigns', CampaignController::class)->middleware('auth');
Route::post('campaigns/{campaign}/execute', [CampaignController::class, 'execute'])
    ->name('campaigns.execute')
    ->middleware('auth');

// Message routes
Route::middleware('auth')->controller(MessageController::class)->group(function () {
    Route::get('messages', 'index')->name('messages.index');
    Route::post('messages/send', 'send')->name('messages.send');
});

Route::middleware('auth')->group(function () {
    Route::resource('groups', GroupController::class);
    Route::post('groups/{group}/add-customers', [GroupController::class, 'addCustomers'])
        ->name('groups.add-customers');
    Route::get('groups/{group}/customers', [GroupController::class, 'customers'])
        ->name('groups.customers');
    Route::delete('groups/{group}/remove-customer/{customer}', [GroupController::class, 'removeCustomer'])
        ->name('groups.remove-customer');
});

Route::resource('templates', TemplateController::class)->middleware('auth');
Route::post('templates/{template}/toggle-status', [TemplateController::class, 'toggleStatus'])
    ->name('templates.toggle-status')
    ->middleware('auth');

// Customer routes
Route::middleware('auth')
    ->controller(CustomerController::class)
    ->prefix('customers')
    ->name('customers.')
    ->group(function () {
        // List and Create
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');

        // Bulk Operations - grouped for better organization
        Route::prefix('bulk')->group(function () {
            Route::post('delete', 'bulkDelete')->name('bulk-delete');
            Route::post('update', 'bulkUpdate')->name('bulk-update');
        });

        // Import/Export Operations
        Route::prefix('data')->group(function () {
            Route::post('import', 'import')->name('import');
            Route::get('export', 'export')->name('export');
        });

        // Single Resource Operations - placed last as they contain dynamic parameters
        Route::get('{customer}', 'show')->name('show');
        Route::get('{customer}/edit', 'edit')->name('edit');
        Route::put('{customer}', 'update')->name('update');
        Route::delete('{customer}', 'destroy')->name('destroy');
    });

Route::middleware('auth')->prefix('api')->group(function () {
    // Group API endpoints
    Route::apiResource('groups', GroupController::class);

    // Campaign API endpoints
    Route::post('campaigns/{campaign}/duplicate', [CampaignController::class, 'duplicate']);
    Route::post('campaigns/{campaign}/resend', [CampaignController::class, 'resend']);
    Route::post('campaigns/{campaign}/execute', [CampaignController::class, 'execute']);
    Route::apiResource('campaigns', CampaignController::class);

    // Message API endpoints
    Route::controller(MessageController::class)->group(function () {
        Route::get('messages', 'index');
        Route::post('messages/send', 'send');
    });
});
