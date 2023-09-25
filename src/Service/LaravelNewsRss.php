<?php

declare(strict_types=1);

namespace App\Service;

class LaravelNewsRss implements RssFeedParserInterface
{

    public const SOURCE = 'laravel-news';

    public string $url = 'https://feed.laravel-news.com/';

    public function parseFeed(string $content): array
    {
        $xml = simplexml_load_string($content);

        $articles = [];

        foreach ($xml->channel->item as $articleItem) {
            $articles[] = [
                'title' => (string) $articleItem->title,
                'content' => trim((string) $articleItem->description),
                'pubDate' => new \DateTime((string) $articleItem->pubDate),
                'guid' => (string) $articleItem->guid,
                'source' => self::SOURCE,
            ];
        }

        return $articles;
    }

    public function supports(string $url)
    {
        // TODO: Implement supports() method.
    }
}
