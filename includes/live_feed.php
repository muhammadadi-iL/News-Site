<?php

function news_fetch_feed_xml($url)
{
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 8,
            'header' => "User-Agent: NewsSiteBot/1.0 (+localhost)\r\nAccept: application/rss+xml, application/xml, text/xml, */*\r\n",
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    $raw = @file_get_contents($url, false, $context);
    if ($raw === false || $raw === '') {
        return null;
    }

    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($raw, 'SimpleXMLElement', LIBXML_NOCDATA);
    return $xml ?: null;
}

function news_live_items($limit = 12, $query = '')
{
    $feeds = [
        ['source' => 'BBC News', 'url' => 'https://feeds.bbci.co.uk/news/rss.xml'],
        ['source' => 'NPR', 'url' => 'https://feeds.npr.org/1001/rss.xml'],
        ['source' => 'The Guardian', 'url' => 'https://www.theguardian.com/world/rss'],
    ];

    $items = [];

    foreach ($feeds as $feed) {
        $xml = news_fetch_feed_xml($feed['url']);
        if (!$xml || !isset($xml->channel->item)) {
            continue;
        }

        foreach ($xml->channel->item as $item) {
            $title = trim((string) $item->title);
            $link = trim((string) $item->link);
            $desc = trim(strip_tags((string) $item->description));
            $pub = trim((string) ($item->pubDate ?? ''));

            if ($title === '' || $link === '') {
                continue;
            }

            if ($query !== '' && stripos($title . ' ' . $desc, $query) === false) {
                continue;
            }

            $image = '';
            if (isset($item->children('media', true)->thumbnail)) {
                $attrs = $item->children('media', true)->thumbnail->attributes();
                $image = isset($attrs['url']) ? (string) $attrs['url'] : '';
            }
            if ($image === '' && isset($item->enclosure)) {
                $enc = $item->enclosure->attributes();
                $type = isset($enc['type']) ? (string) $enc['type'] : '';
                if (isset($enc['url']) && strpos($type, 'image/') === 0) {
                    $image = (string) $enc['url'];
                }
            }

            $items[] = [
                'title' => $title,
                'link' => $link,
                'summary' => function_exists('mb_substr') ? mb_substr($desc, 0, 180) : substr($desc, 0, 180),
                'source' => $feed['source'],
                'published' => $pub !== '' ? date('c', strtotime($pub) ?: time()) : date('c'),
                'image' => $image,
                'type' => 'live',
            ];
        }
    }

    usort($items, function ($a, $b) {
        return strcmp($b['published'], $a['published']);
    });

    return array_slice($items, 0, $limit);
}
