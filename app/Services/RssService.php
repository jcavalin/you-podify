<?php

namespace App\Services;

class RssService
{
    public function generate(array $channelInfo)
    {
        $rss = new \SimpleXMLElement('<rss/>', 0, false, 'itunes', true);
        $rss->addAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
        $rss->addAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
        $rss->addAttribute('version', '1.0');

        $channel = $rss->addChild('channel');
        $channel->addChild('title', htmlspecialchars($channelInfo['title']));
        $channel->addChild('link', htmlspecialchars($channelInfo['link']));
        $channel->addChild('description', htmlspecialchars($channelInfo['description']));
        $channel->addChild('language', htmlspecialchars($channelInfo['language']));
        $channel->addChild('itunes:image', null, 'itunes')->addAttribute('href', $channelInfo['image']);
        $channel->addChild('itunes:category', null, 'itunes')->addAttribute('text', $channelInfo['category']);
        $channel->addChild('itunes:explicity', 'no', 'itunes');
        $channel->addChild('itunes:author', htmlspecialchars($channelInfo['author']), 'itunes');
        $image = $channel->addChild('image');
        $image->addChild('url', htmlspecialchars($channelInfo['image']));
        $image->addChild('title', htmlspecialchars($channelInfo['title']));
        $image->addChild('link', htmlspecialchars($channelInfo['link']));
        $image->addChild('widht', 32);
        $image->addChild('height', 32);

        foreach ($channelInfo['episodes'] as $episode) {
            $item = $channel->addChild('item');
            $item->addChild('title', htmlspecialchars($episode['title']));
            $item->addChild('itunes:author', htmlspecialchars($episode['author']), 'itunes');
            $item->addChild('itunes:summary', htmlspecialchars($episode['summary']), 'itunes');
            $item->addChild('itunes:duration', htmlspecialchars($episode['duration']), 'itunes');
            $item->addChild('itunes:image', htmlspecialchars($episode['image']), 'itunes');
            $enclosure = $item->addChild('enclosure');
            $enclosure->addAttribute('url', $episode['audio']);
            $enclosure->addAttribute('type', 'audio/mpeg');
            $item->addChild('guid', htmlspecialchars($episode['guid']));
            $item->addChild('pubDate', htmlspecialchars($episode['date']));
        }

        return $rss->asXML();
    }
}