<?php


namespace App\Entity;
use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;




/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`product`")
 * @ApiResource
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @group("post:read")
     */
    private $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

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
     * @return mixed
     */
    public function getDetail()
    {
        return $this->Detail;
    }

    /**
     * @param mixed $Detail
     */
    public function setDetail($Detail): void
    {
        $this->Detail = $Detail;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @ORM\ManyToOne(targetEntity=Store::class, inversedBy="Products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Store;

    /**
     * @ORM\Column(type="text")
     */
    private $Detail;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @group("post:read")
     */
    private $price;

}