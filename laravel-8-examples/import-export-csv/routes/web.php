<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriberCtrl;

Route::get('/', function () {
    return redirect('import');
});

Route::GET('import', [SubscriberCtrl::class, 'import'])->name('import');
Route::POST('import', [SubscriberCtrl::class, 'import_post'])->name('import.post');
Route::GET('export', [SubscriberCtrl::class, 'export'])->name('export');