<?php

namespace App\Services;

use Google\Client;
use Google\Service\YouTube;

class EpisodeService
{
    protected $youtube;

    public function __construct()
    {
        $client = new Client();
        $client->setApplicationName('You-Podify');
        $client->setDeveloperKey(env('YOUTUBE_API_KEY'));
        $this->youtube = new YouTube($client);
    }

    public function getList(string $channelName, int $maxResults = 0)
    {
        $response = $this->youtube->search->listSearch('snippet', [
            'q' => "@$channelName",
            'type' => 'channel',
            'maxResults' => 1,
        ]);

        if (count($response->getItems()) > 0) {
            $channelId = $response->getItems()[0]['id']['channelId'];
        } else {
            return null;
        }

        $response = $this->youtube->channels->listChannels('contentDetails,snippet,statistics,topicDetails', [
            'id' => $channelId,
        ]);

        if (count($response->getItems()) > 0) {
            $channelInfo = $response->getItems()[0];
            $channel = [
                'title' => $channelInfo['snippet']['title'],
                'link' => "https://www.youtube.com/{$channelInfo['snippet']['customUrl']}",
                'description' => $channelInfo['snippet']['description'],
                'image' => $channelInfo['snippet']['thumbnails']['high']['url'],
                'author' => $channelInfo['snippet']['title'],
                'category' => str_replace('https://en.wikipedia.org/wiki/', '', implode(', ', $channelInfo['topicDetails']['topicCategories'])),
                'language' => $channelInfo['snippet']['defaultLanguage'],
                'episodes' => []
            ];
        } else {
            return null;
        }

        try {
            $pageToken = null;
            do {
                $response = $this->youtube->search->listSearch('snippet', [
                    'channelId' => $channelId,
                    'maxResults' => 50, 
                    'order' => 'date',  
                    'type' => 'video',
                    'pageToken' => $pageToken,
                ]);
                
                foreach($response->getItems() as $item) {
                    $channel['episodes'][] = [
                        'title' => $item['snippet']['title'],
                        'author' => $item['snippet']['title'],
                        'summary' => $item['snippet']['description'],
                        'audio' => url("/episode/{$item['id']['videoId']}"),
                        'image' => $item['snippet']['thumbnails']['high']['url'],
                        'guid' => $item['id']['videoId'],
                        'date' => $item['snippet']['publishedAt'],
                        'duration' => $this->getVideoDuration($item['id']['videoId'])
                    ];

                    if ($maxResults > 0 && count($channel['episodes']) >= $maxResults) {
                        return $channel;
                    }
                }

                $pageToken = $response->getNextPageToken();

            } while ($pageToken);

        } catch (\Exception $e) {
            throw $e;
        }

        return $channel;
    }

    public function getVideoDuration($videoId)
    {
        try {
            $response = $this->youtube->videos->listVideos('contentDetails', [
                'id' => $videoId,
            ]);

            if (count($response->getItems()) > 0) {
                $durationISO8601 = $response->getItems()[0]['contentDetails']['duration'];
                return $this->convertISO8601Duration($durationISO8601);
            } else {
                return null;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function convertISO8601Duration($duration)
    {
        $interval = new \DateInterval($duration);
        return sprintf(
            '%02d:%02d:%02d',
            ($interval->h + ($interval->d * 24)), 
            $interval->i,
            $interval->s 
        );
    }
}
