<?php

declare(strict_types=1);

namespace App\Service;

class TheVergeRss implements RssFeedParserInterface
{
    public const SOURCE = 'theverge';

    public string $url = 'https://www.theverge.com/tech/rss/index.xml';

    public function parseFeed(string $content): array
    {
        $xml = simplexml_load_string($content);
        $articles = [];

        foreach ($xml->entry as $articleItem) {
            $articles[] = [
                'title' => (string) $articleItem->title,
                'content' => trim((string) $articleItem->content),
                'source' => self::SOURCE,
                'pubDate' => new \DateTime((string) $articleItem->published),
                'guid' => (string) $articleItem->id,
            ];
        }

        return $articles;
    }

    public function supports(string $url)
    {
        // TODO: Implement supports() method.
    }
}
