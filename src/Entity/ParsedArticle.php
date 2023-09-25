<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: "parsed_articles")]
class ParsedArticle
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: "integer")]
    private int $id;

    #[Column(type: "string")]
    private string $guid;

    #[Column(type: "datetime")]
    private \DateTime $parsedAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * @param string $guid
     */
    public function setGuid(string $guid): void
    {
        $this->guid = $guid;
    }

    /**
     * @return \DateTime
     */
    public function getParsedAt(): \DateTime
    {
        return $this->parsedAt;
    }

    /**
     * @param \DateTime $parsedAt
     */
    public function setParsedAt(\DateTime $parsedAt): void
    {
        $this->parsedAt = $parsedAt;
    }
}
