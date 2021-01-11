<?php

namespace App\Entity;

use App\Repository\AliasDiffRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AliasDiffRepository::class)
 */
class AliasDiff
{
    const IS_LOCAL = 'local';
    const IS_DISTANT = 'distant';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $alias;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $existOn;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getExistOn(): ?string
    {
        return $this->existOn;
    }

    public function isDistant(): bool
    {
        return $this->getExistOn() === self::IS_DISTANT;
    }

    public function isLocal(): bool
    {
        return $this->getExistOn() === self::IS_LOCAL;
    }

    public function setExistOn(string $existOn): self
    {
        $this->existOn = $existOn;

        return $this;
    }

    public function toAlias(): Alias
    {
        return (new Alias())
            ->setAliasEmail($this->getAlias())
            ->setRealEmail($this->getEmail());
    }
}
