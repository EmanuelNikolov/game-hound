<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="games")
 */
class Game
{

    public const COVER_ENDPOINT = 'https://images.igdb.com/igdb/image/upload/t_';

    public const FALLBACK_IMG = '';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $summary;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firstReleaseDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cover;

    /**
     * @return null|int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return null|string
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @param null|string $summary
     *
     * @return void
     */
    public function setSummary(?string $summary): void
    {
        $this->summary = $summary;
    }

    /**
     * @return null|\DateTime
     */
    public function getFirstReleaseDate(): ?\DateTime
    {
        return $this->firstReleaseDate;
    }

    /**
     * @param null|float $firstReleaseDate
     *
     * @return void
     */
    public function setFirstReleaseDate(?float $firstReleaseDate): void
    {
        if (null !== $firstReleaseDate) {
            $formattedDate = (int)($firstReleaseDate / 1000);
            $this->firstReleaseDate = (new \DateTime())->setTimestamp($formattedDate);
        }
    }

    /**
     * @return null|string
     */
    public function getCover(): ?string
    {
        return $this->cover;
    }

    /**
     * @param null|array $cover
     */
    public function setCover(?array $cover): void
    {
        if (null !== $cover) {
            $this->cover = array_key_exists('cloudinary_id', $cover)
              ? $cover['cloudinary_id']
              : $cover['url'];
        } else {
            $this->cover = self::FALLBACK_IMG;
        }
    }

    public function getCoverUrl(?string $size): string
    {
        // Check if an URL is stored or a Cloudinary-ID
        if (false !== strpos($this->cover, '/')) {
            return $this->cover;
        }

        return self::COVER_ENDPOINT . $size . '/' . $this->cover . '.png';
    }
}