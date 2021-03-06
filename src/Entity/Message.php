<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 * @ApiResource
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @group("post:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="SentMessages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="RecievedMessages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Reciever;
    /**
     * @ORM\ManyToOne(targetEntity=Store::class, inversedBy="StoreMessages")
     * @ORM\JoinColumn(nullable=true)
     */
    private $Store;

    /**
     * @return mixed
     */
    public function getStore()
    {
        return $this->Store;
    }

    /**
     * @param mixed $Store
     */
    public function setStore($Store): void
    {
        $this->Store = $Store;
    }

    /**
     * @ORM\Column(type="datetime")
     * @group("post:read")
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     * @group("post:read")
     */
    private $content;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReciever(): ?User
    {
        return $this->Reciever;
    }

    public function setReciever(?User $Reciever): self
    {
        $this->Reciever = $Reciever;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}