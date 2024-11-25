<?php

namespace App\Http\Controllers;

use App\Services\DownloadService;

class EpisodeController extends Controller
{
    public function __construct(protected DownloadService $downloadService)
    {
    }

    public function getEpisode(string $episodeId)
    {
        $filePath = $this->downloadService->download($episodeId);
        
        if (!$filePath) {
            return response('', 404);
        }
        
        return response()->download($filePath, "$episodeId.mp3", ['Content-Type' => 'audio/mpeg']);
    }
}
