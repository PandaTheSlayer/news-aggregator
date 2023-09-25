<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;

class TechCrunchRss implements RssFeedParserInterface
{
    public const SOURCE = 'techcrunch';

    public string $url = 'https://techcrunch.com/feed';

    public function parseFeed(string $content): array
    {
        $xml = simplexml_load_string($content);
        $articles = [];

        foreach ($xml->channel->item as $articleItem) {
            $articles[] = [
                'title' => (string) $articleItem->title,
                'content' => trim((string) $articleItem->description),
                'source' => self::SOURCE,
                'pubDate' => new DateTime((string) $articleItem->pubDate),
                'guid' => (string) $articleItem->guid,
            ];
        }

        return $articles;
    }

    public function supports(string $url): bool
    {
        return $url === 'techcrunch';
    }
}
