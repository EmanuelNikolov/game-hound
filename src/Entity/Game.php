<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="games")
 */
class Game
{

    public const COVER_ENDPOINT = 'https://images.igdb.com/igdb/image/upload/';

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
     * @ORM\Column(type="bigint", nullable=true)
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
     * @return float|null
     */
    public function getFirstReleaseDate(): ?float
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
        $this->firstReleaseDate = (int)$firstReleaseDate;
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
        $this->cover = array_key_exists('cloudinary_id', $cover)
          ? $cover['cloudinary_id']
          : $cover['url'];
    }

    public function getCoverUrl(string $size): string
    {
        // Check if an URL is stored or a Cloudinary-ID
        if (false !== strpos($this->cover, '/')) {
            return $this->cover;
        }

        return self::COVER_ENDPOINT . $size . '/' . $this->cover . '.png';
    }
}