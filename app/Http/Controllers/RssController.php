<?php

namespace App\Http\Controllers;

use App\Services\EpisodeService;
use App\Services\RssService;

class RssController extends Controller
{
    public function __construct(protected EpisodeService $episodeService, protected RssService $rssService)
    {
    }

    public function getRss(string $channelId)
    {
        $result = $this->episodeService->getList($channelId, 50);
        
        if (!$result) {
            return response('', 404);
        }
        
        return response($this->rssService->generate($result), 200)->header('Content-Type', 'application/xml');
    }
}
