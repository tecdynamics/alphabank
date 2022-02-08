<?php
use App\Http\Middleware\VerifyCsrfToken;

Route::group(['namespace' => 'Botble\Alphabank\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::any('alphabank/payment/callback', [
        'as'   => 'alphabank.payment.callback',
        'uses' => 'AlphabankController@paymentCallback',
    ])->withoutMiddleware(VerifyCsrfToken::class);


    Route::get('alphabank/payment/redirect', [
        'as'   => 'alphabank.payment.redirect',
        'uses' => 'AlphabankController@paymentredirect',
    ])->withoutMiddleware(VerifyCsrfToken::class);
});
