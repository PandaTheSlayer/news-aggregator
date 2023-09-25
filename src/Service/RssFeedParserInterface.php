<?php

declare(strict_types=1);

namespace App\Service;

interface RssFeedParserInterface
{
    public function parseFeed(string $content): array;

    public function supports(string $url);
}
