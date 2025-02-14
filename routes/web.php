<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\EventController;

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
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
});

//event response route
Route::get('event/response/{event}/{user}/{status}', [EventController::class, 'respondToEvent'])->name('event.response');
Route::get('event/response/msg', [EventController::class, 'respondMsg'])->name('event.msg');

//admin routes
Route::middleware(['auth','admin'])->group(function () {
    //Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/dashboard', function () {
        return view('dashboard');
    })->name('admin.dashboard');
    Route::get('/admin/users', [UserController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/change-status', [UserController::class, 'changeStatus'])->name('admin.changeStatus');
    
});

require __DIR__.'/auth.php';
