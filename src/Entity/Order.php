<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;


/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 * @ApiResource
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @group("post:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Store::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Store;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Customer;

    /**
     * @ORM\Column(type="integer")
     * @group("post:read")
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     * @group("post:read")
     */
    private $date;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @group("post:read")
     */
    private $detail;

    /**
     * @ORM\Column(type="integer")
     * @group("post:read")
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCustomer(): ?User
    {
        return $this->Customer;
    }

    public function setCustomer(?User $Customer): self
    {
        $this->Customer = $Customer;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

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

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
