<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AliasRepository")
 */
class Alias
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $realEmail = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $aliasEmail = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRealEmail(): ?string
    {
        return $this->realEmail;
    }

    public function setRealEmail(string $realEmail): self
    {
        $this->realEmail = $realEmail;

        return $this;
    }

    public function getAliasEmail(): ?string
    {
        return $this->aliasEmail;
    }

    public function setAliasEmail(string $aliasEmail): self
    {
        $this->aliasEmail = $aliasEmail;

        return $this;
    }

    public function __toString(): string
    {
        return $this->aliasEmail;
    }

    public function getDomain(): string
    {
        $email = explode('@', $this->realEmail);

        return count($email) < 2 ? '' : "@$email[1]";
    }
}
