<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RssController;


Route::get('/rss/{channelId}', [RssController::class, 'getRss']);
