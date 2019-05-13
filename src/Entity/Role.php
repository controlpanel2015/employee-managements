<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
*/
class Role
{
    /**
    * @ORM\Id()
    * @ORM\Column(type="string", length=100)
    */
    private $id;

    /**
    * @ORM\Column(type="string", length=255)
    */
    private $name;

    public function __construct(string $id = null)
    {
        $this->id = $id;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

    return $this;
    }

    public function __toString()
    {
        return $this->id;
    }
}
