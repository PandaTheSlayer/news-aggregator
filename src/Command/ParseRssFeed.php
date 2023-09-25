<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Article;
use App\Entity\ParsedArticle;
use App\Service\RssFeedParserInterface;
use App\Service\YamlConfigRss;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'parse-feed')]
class ParseRssFeed extends Command
{
    public function __construct(
        private readonly iterable $parsers,
        private readonly YamlConfigRss $yamlConfigRss,
        private readonly ClientInterface $client,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $articles = [];
        /** @var RssFeedParserInterface $parser */
        foreach ($this->parsers as $parser) {
            try {
                $articles = [
                    ...$articles,
                    ...$parser->parseFeed(
                        $this->client->get($parser->url)->getBody()->getContents()
                    )
                ];
            } catch (GuzzleException $e) {
                dump($e->getMessage());
            }
        }

        $articles = [...$articles, ...$this->yamlConfigRss->parseFeed()];

        foreach ($articles as $articleData) {
            if (!$this->isArticleParsed((string) $articleData['guid'])) {
                $this->createAndPersistArticle($articleData);
            }
        }

        $this->entityManager->flush();
        return Command::SUCCESS;
    }

    private function isArticleParsed(string $guid): bool
    {
        return (bool)$this->entityManager->getRepository(ParsedArticle::class)->findOneBy(['guid' => $guid]);
    }

    private function createAndPersistArticle(array $articleData): void
    {
        $article = new Article();
        $article->setTitle((string) $articleData['title']);
        $article->setContent((string) $articleData['content']);
        $article->setSource((string) $articleData['source']);
        $article->setPublishedAt($articleData['pubDate']);
        $article->setGuid((string) $articleData['guid']);

        $this->entityManager->persist($article);

        $this->markArticleAsParsed((string) $articleData['guid']);
    }

    private function markArticleAsParsed(string $guid): void
    {
        $parsedArticle = new ParsedArticle();
        $parsedArticle->setGuid($guid);
        $parsedArticle->setParsedAt(new \DateTime());

        $this->entityManager->persist($parsedArticle);
    }

}
