<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::middleware('auth')->group(function () {
    Route::get('print/analysis', [PrintController::class, 'analysis'])->name('print.analysis');
    Route::get('print/compare', [PrintController::class, 'compare'])->name('print.compare');
    Route::get('print/top-or-under-five', [PrintController::class, 'topOrUnderFive'])->name('print.top-or-under-five');
    Route::get('print/unreport-branches', [PrintController::class, 'unreportBranches'])->name('print.unreport-branches');
});
