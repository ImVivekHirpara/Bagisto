<?php

/**
 * Store front routes.
 */
require 'store-front-routes.php';

/**
 * Customer routes. All routes related to customer
 * in storefront will be placed here.
 */
require 'customer-routes.php';

/**
 * Checkout routes. All routes related to checkout like
 * cart, coupons, etc will be placed here.
 */
require 'checkout-routes.php';

// Add this to packages/Webkul/Shop/src/Routes/web.php

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    
    // Leaving Soon Products Route
    Route::get('/leaving-soon', 'Webkul\Shop\Http\Controllers\LeavingSoonController@index')->defaults('_config', [
        'view' => 'shop::leaving-soon.index'
    ])->name('shop.leaving-soon.index');
    
});