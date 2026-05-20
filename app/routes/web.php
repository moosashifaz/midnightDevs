<?php

use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProviderDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketplaceController::class, 'home'])->name('home');
Route::get('/c/{category}', [MarketplaceController::class, 'category'])->name('category');
Route::get('/listing/{listing:slug}', [MarketplaceController::class, 'listing'])->name('listing');
Route::post('/island/{island:slug}', [MarketplaceController::class, 'setIsland'])->name('island.set');

Route::middleware(['auth'])->group(function () {
    Route::get('/checkout/{listing:slug}', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/{listing:slug}', [OrderController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order:reference}', [OrderController::class, 'show'])->name('orders.show');

    Route::prefix('provider')->name('provider.')->group(function () {
        Route::get('/dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');
        Route::post('/orders/{order:reference}/fulfill', [ProviderDashboardController::class, 'fulfill'])->name('orders.fulfill');
    });

    Route::view('profile', 'profile')->name('profile');

    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isProvider()) {
            return redirect()->route('provider.dashboard');
        }
        return redirect()->route('home');
    })->name('dashboard');

    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    })->name('logout');
});

require __DIR__.'/auth.php';
