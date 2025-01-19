<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TemplateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [DashboardController::class, 'index'])->name('home');

// Protected routes
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Customer routes
    Route::resource('customers', CustomerController::class);
    Route::controller(CustomerController::class)->group(function () {
        Route::post('customers/bulk-delete', 'bulkDelete')->name('customers.bulk-delete');
        Route::post('customers/import', 'import')->name('customers.import');
    });

    // Group routes
    Route::resource('groups', GroupController::class);

    // Campaign routes
    Route::resource('campaigns', CampaignController::class);
    Route::post('campaigns/{campaign}/execute', [CampaignController::class, 'execute'])
        ->name('campaigns.execute');

    // Message routes
    Route::controller(MessageController::class)->group(function () {
        Route::get('messages', 'index')->name('messages.index');
        Route::post('messages/send', 'send')->name('messages.send');
    });

// API routes (move these to routes/api.php)
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('groups', GroupController::class);

        // Campaign routes
        Route::resource('campaigns', CampaignController::class);
        Route::post('campaigns/{campaign}/execute', [CampaignController::class, 'execute'])
            ->name('campaigns.execute');

                Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export');
                Route::post('customers/import', [CustomerController::class, 'import'])->name('customers.import');
                Route::post('customers/bulk-delete', [CustomerController::class, 'bulkDelete'])->name('customers.bulk-delete');
                Route::post('customers/bulk-update', [CustomerController::class, 'bulkUpdate'])->name('customers.bulk-update');
                Route::resource('customers', CustomerController::class);

                Route::resource('groups', GroupController::class);
                Route::post('groups/{group}/add-customers', [GroupController::class, 'addCustomers'])
                    ->name('groups.add-customers');
                Route::get('groups/{group}/customers', [GroupController::class, 'customers'])
                    ->name('groups.customers');
                Route::delete('groups/{group}/remove-customer/{customer}', [GroupController::class, 'removeCustomer'])
                    ->name('groups.remove-customer');

                    Route::resource('templates', TemplateController::class);
                    Route::post('templates/{template}/toggle-status', [TemplateController::class, 'toggleStatus'])
                        ->name('templates.toggle-status');
            
                        Route::prefix('api')->group(function () {
                            // Customer API endpoints
                            Route::apiResource('customers', CustomerController::class);
                            Route::controller(CustomerController::class)->group(function () {
                                Route::post('customers/bulk-delete', 'bulkDelete');
                                Route::post('customers/import', 'import');
                            });
                        
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