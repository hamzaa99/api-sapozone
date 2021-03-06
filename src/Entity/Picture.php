<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;


/**
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 * @ApiResource
 */
class Picture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @group("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @group("post:read")
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity=Store::class, inversedBy="Pictures")
     */
    private $Store;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getStore(): ?Store
    {
        return $this->Store;
    }

    public function setStore(?Store $Store): self
    {
        $this->Store = $Store;

        return $this;
    }
}
