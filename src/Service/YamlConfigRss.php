<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\ClientInterface;
use SimpleXMLElement;
use Symfony\Component\Yaml\Yaml;

class YamlConfigRss
{
    private array $parsedConfig;

    public function __construct(private readonly ClientInterface $client)
    {
        $this->parsedConfig = Yaml::parseFile(__DIR__ . '/../../config/rss.yml');
    }

    public function parseFeed(): array
    {
        $articles = [];
        foreach ($this->parsedConfig as $source => $config) {
            $xml = simplexml_load_string($this->client->get($config['url'])->getBody()->getContents());

            $items = $this->mapYamlToProperty($config['destination'], $xml);
            foreach ($items as $articleItem) {
                $articles[] = [
                    'title' => $this->mapYamlToProperty($config['title'], $articleItem),
                    'content' =>trim((string)$this->mapYamlToProperty($config['content'], $articleItem)),
                    'source' => $source,
                    'pubDate' => new \DateTime((string) $this->mapYamlToProperty($config['pubDate'], $articleItem)),
                    'guid' => (string) $this->mapYamlToProperty((string) $config['guid'], $articleItem),
                ];
            }
        }

        return $articles;
    }

    private function mapYamlToProperty(string $yamlKey, SimpleXMLElement|false $object): mixed
    {
        // Split the YAML key into segments
        $segments = explode('.', $yamlKey);

        // Start with the root object
        $currentObject = $object;

        // Traverse the object hierarchy
        foreach ($segments as $segment) {
            // Check if the current segment is an array index
            if (is_array($currentObject) && isset($currentObject[$segment])) {
                $currentObject = $currentObject[$segment];
            }
            // Check if the current segment is an object property
            elseif (is_object($currentObject) && property_exists($currentObject, $segment)) {
                $currentObject = $currentObject->{"$segment"};
            }
            // Handle cases where the key does not exist in the structure
            else {
                return null; // or throw an exception, depending on your use case
            }
        }

        return $currentObject;
    }
}
