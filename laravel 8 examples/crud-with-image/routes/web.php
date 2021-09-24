<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostCtrl;

Route::get('/', function () {
    return redirect('/posts');
});
Route::resource('posts', PostCtrl::class);