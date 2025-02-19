<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\EventController;   
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WebhookController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// event routes
Route::middleware(['auth'])->group(function () {
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::middleware('subscription')->group(function () {
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
    });
    //user subscription routes   
    Route::get('/subscription/plans', [SubscriptionController::class, 'index'])->name('subscription.plans');
    Route::post('/subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
    Route::get('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::get('/subscription/my-plan', [SubscriptionController::class, 'myPlan'])->name('subscription.myPlan');
    Route::post('/subscription/cancel-my-plan', [SubscriptionController::class, 'cancelMyPlan'])->name('subscription.cancelMyPlan');
});

//event response route
Route::get('event/response/{event}/{user}/{status}', [EventController::class, 'respondToEvent'])->name('event.response');
Route::get('event/response/msg', [EventController::class, 'respondMsg'])->name('event.msg');

//admin routes
Route::middleware(['auth','admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('dashboard');
    })->name('admin.dashboard');
    Route::get('/admin/users', [UserController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/change-status', [UserController::class, 'changeStatus'])->name('admin.changeStatus');
    //plans routes
    Route::get('/admin/plans', [PlanController::class, 'index'])->name('admin.plans');
    Route::get('/admin/plans/create', [PlanController::class, 'create'])->name('admin.plans.create');
    Route::post('/admin/plans', [PlanController::class, 'store'])->name('admin.plans.store');
});

//stripe webhook
Route::post('/webhook/stripe', [WebhookController::class, 'handleWebhook'])->name('webhook.stripe')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]); // Disable CSRF token verification for webhook

require __DIR__.'/auth.php';
