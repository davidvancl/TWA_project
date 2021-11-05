<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=false)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateUpdated;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTo;

    /** @var int
     * @ORM\Column(name="creator_id", type="integer")
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="creator_id", referencedColumnName="id")
     */
    private $creator_id;

    /**
     * @ORM\Column(type="string", length=20, unique=false)
     */
    private $section;

    /**
     * @ORM\Column(type="string", length=1000, unique=false)
     */
    private $body;

    /**
     * @ORM\Column(type="string", length=20, unique=false)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=10, unique=false)
     */
    private $visibility;

    /**
     * @ORM\Column(type="string", length=50, unique=false, nullable=true)
     */
    private $hrefText;

    /**
     * @ORM\Column(type="string", length=100, unique=false, nullable=true)
     */
    private $href;

    /**
     * @ORM\Column(type="string", length=100, unique=false, nullable=true)
     */
    private $imagePath;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @return mixed
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param mixed $visibility
     */
    public function setVisibility($visibility): void
    {
        $this->visibility = $visibility;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return mixed
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @return mixed
     */
    public function getHrefText()
    {
        return $this->hrefText;
    }

    /**
     * @param mixed $href
     */
    public function setHref($href): void
    {
        $this->href = $href;
    }

    /**
     * @param mixed $hrefText
     */
    public function setHrefText($hrefText): void
    {
        $this->hrefText = $hrefText;
    }

    /**
     * @return int
     */
    public function getCreatorId(): int
    {
        return $this->creator_id;
    }

    /**
     * @return mixed
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @return mixed
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * @param mixed $imagePath
     */
    public function setImagePath($imagePath): void
    {
        $this->imagePath = $imagePath;
    }

    /**
     * @return mixed
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @return mixed
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body): void
    {
        $this->body = $body;
    }

    /**
     * @param int $creator_id
     */
    public function setCreatorId(int $creator_id): void
    {
        $this->creator_id = $creator_id;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @param mixed $dateTo
     */
    public function setDateTo($dateTo): void
    {
        $this->dateTo = $dateTo;
    }

    /**
     * @param mixed $dateUpdated
     */
    public function setDateUpdated($dateUpdated): void
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @param mixed $section
     */
    public function setSection($section): void
    {
        $this->section = $section;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }
}
