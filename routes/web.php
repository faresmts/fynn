<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Pages\Dashboard;
use App\Livewire\Pages\ExpensesPage;
use App\Livewire\Pages\ReceiptsPage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/receipts', ReceiptsPage::class)->name('receipts');
    Route::get('/debts', ReceiptsPage::class)->name('debts');
    Route::get('/expenses', ExpensesPage::class)->name('expenses');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});

require __DIR__.'/auth.php';
