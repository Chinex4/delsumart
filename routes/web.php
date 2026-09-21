<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDisputeController;
use App\Http\Controllers\AdminMarketplaceController;
use App\Http\Controllers\AdminPayoutController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AdminSystemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\KycDocumentController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\PrivateEvidenceController;
use App\Http\Controllers\TransactionController;
use App\Models\Listing;
use Illuminate\Support\Facades\Route;

Route::get(
    '/',
    fn () => view('home', [
        'recent' => Listing::with(['seller.verification', 'images'])
            ->where('status', 'active')
            ->latest()
            ->take(8)
            ->get(),
        'listingCount' => Listing::where('status', 'active')->count(),
    ]),
)->name('home');
Route::get('/marketplace', [ListingController::class, 'index'])->name(
    'listings.index',
);
Route::get('/marketplace/{listing}', [ListingController::class, 'show'])->name(
    'listings.show',
);
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:4,1')
        ->name('register.store');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('login.store');
    Route::view('/mfa', 'auth.mfa')->name('mfa.show');
    Route::post('/mfa', [AuthController::class, 'verifyMfa'])
        ->middleware('throttle:8,1')
        ->name('mfa.verify');
    Route::get('/forgot-password', [
        PasswordResetController::class,
        'requestForm',
    ])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])
        ->middleware('throttle:3,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [
        PasswordResetController::class,
        'resetForm',
    ])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
Route::post('/paystack/webhook', [PaymentController::class, 'webhook'])
    ->middleware('throttle:120,1')
    ->name('payments.webhook');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'overview'])->name(
        'dashboard',
    );
    Route::get('/account', [DashboardController::class, 'account'])->name(
        'account',
    );
    Route::get('/account/listings', [
        DashboardController::class,
        'listings',
    ])->name('account.listings');
    Route::get('/account/purchases', [
        DashboardController::class,
        'purchases',
    ])->name('account.purchases');
    Route::get('/account/sales', [DashboardController::class, 'sales'])->name(
        'account.sales',
    );
    Route::get('/account/transactions', [
        DashboardController::class,
        'transactions',
    ])->name('account.transactions');
    Route::get('/account/disputes', [
        DashboardController::class,
        'disputes',
    ])->name('account.disputes');
    Route::get('/account/payouts', [PayoutController::class, 'index'])->name(
        'payouts.index',
    );
    Route::post('/account/payouts/bank/otp', [
        PayoutController::class,
        'requestBankOtp',
    ])
        ->middleware('throttle:3,5')
        ->name('payouts.bank.otp');
    Route::post('/account/payouts/bank/otp/verify', [
        PayoutController::class,
        'verifyBankOtp',
    ])
        ->middleware('throttle:8,5')
        ->name('payouts.bank.otp.verify');
    Route::get('/account/payouts/banks', [PayoutController::class, 'banks'])
        ->middleware('throttle:30,1')
        ->name('payouts.banks');
    Route::post('/account/payouts/bank/resolve', [
        PayoutController::class,
        'resolveBank',
    ])
        ->middleware('throttle:20,1')
        ->name('payouts.bank.resolve');
    Route::post('/account/payouts/bank', [PayoutController::class, 'storeBank'])
        ->middleware('throttle:5,5')
        ->name('payouts.bank.store');
    Route::post('/account/payouts', [PayoutController::class, 'store'])
        ->middleware(['verified.student', 'throttle:5,10'])
        ->name('payouts.store');
    Route::get('/verification', [KycController::class, 'show'])->name(
        'kyc.show',
    );
    Route::post('/verification', [KycController::class, 'store'])
        ->middleware('throttle:4,10')
        ->name('kyc.store');
    Route::get('/verification/{verification}/documents/{type}', [
        KycDocumentController::class,
        'show',
    ])->name('kyc.documents.show');
    Route::get('/disputes/{dispute}/evidence', [
        PrivateEvidenceController::class,
        'show',
    ])->name('disputes.evidence');
    Route::middleware('verified.student')->group(function () {
        Route::get('/account/listings/create', [
            ListingController::class,
            'create',
        ])->name('listings.create');
        Route::post('/listings', [ListingController::class, 'store'])->name(
            'listings.store',
        );
        Route::delete('/listings/{listing}', [
            ListingController::class,
            'destroy',
        ])->name('listings.destroy');
        Route::post('/checkout/{listing}', [
            PaymentController::class,
            'initialize',
        ])
            ->middleware('throttle:5,1')
            ->name('payments.initialize');
        Route::get('/payments/callback', [
            PaymentController::class,
            'callback',
        ])->name('payments.callback');
        Route::post('/transactions/{transaction}/confirm', [
            TransactionController::class,
            'confirm',
        ])->name('transactions.confirm');
        Route::post('/transactions/{transaction}/disputes', [
            DisputeController::class,
            'store',
        ])->name('disputes.store');
    });
    Route::prefix('admin')
        ->middleware('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/', [AdminController::class, 'dashboard'])->name(
                'dashboard',
            );
            Route::patch('/system/settings', [AdminSystemController::class, 'update'])->name('system.settings.update');
            Route::post('/system/clear-caches', [AdminSystemController::class, 'clearCaches'])
                ->middleware('throttle:3,10')
                ->name('system.clear-caches');
            Route::get('/students', [
                AdminStudentController::class,
                'index',
            ])->name('students');
            Route::get('/students/{student}', [
                AdminStudentController::class,
                'show',
            ])->name('students.show');
            Route::patch('/students/{student}/status', [
                AdminStudentController::class,
                'status',
            ])->name('students.status');
            Route::get('/verifications', [
                AdminController::class,
                'verifications',
            ])->name('verifications');
            Route::get('/verifications/{verification}', [
                AdminController::class,
                'verification',
            ])->name('verifications.show');
            Route::patch('/verifications/{verification}', [
                AdminController::class,
                'decide',
            ])->name('verifications.decide');
            Route::get('/listings', [
                AdminMarketplaceController::class,
                'listings',
            ])->name('listings');
            Route::get('/transactions', [
                AdminMarketplaceController::class,
                'transactions',
            ])->name('transactions');
            Route::get('/payouts', [
                AdminPayoutController::class,
                'index',
            ])->name('payouts');
            Route::patch('/payouts/{payout}', [
                AdminPayoutController::class,
                'update',
            ])->name('payouts.update');
            Route::get('/disputes', [
                AdminDisputeController::class,
                'index',
            ])->name('disputes');
            Route::patch('/disputes/{dispute}', [
                AdminDisputeController::class,
                'resolve',
            ])->name('disputes.resolve');
            Route::get('/fraud-flags', [AdminController::class, 'flags'])->name(
                'flags',
            );
            Route::patch('/fraud-flags/{flag}', [
                AdminController::class,
                'reviewFlag',
            ])->name('flags.review');
            Route::get('/audit-logs', [AdminController::class, 'audits'])->name(
                'audits',
            );
        });
});
