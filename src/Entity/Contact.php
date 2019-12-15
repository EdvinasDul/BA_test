<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 */
class Contact
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $full_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone_number;

    /**
     * @ORM\Column(type="integer")
     */
    private $owned_by;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $shared_with;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function setFullName(string $full_name): self
    {
        $this->full_name = $full_name;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): self
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getOwnedBy(): ?int
    {
        return $this->owned_by;
    }

    public function setOwnedBy(int $owned_by): self
    {
        $this->owned_by = $owned_by;

        return $this;
    }

    public function getSharedWith(): ?int
    {
        return $this->shared_with;
    }

    public function setSharedWith(?int $shared_with): self
    {
        $this->shared_with = $shared_with;

        return $this;
    }
}
