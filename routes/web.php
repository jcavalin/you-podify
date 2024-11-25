<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\RssController;


Route::get('/rss/{channelId}', [RssController::class, 'getRss']);
Route::get('/episode/{episodeId}', [EpisodeController::class, 'getEpisode']);
