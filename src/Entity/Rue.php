<?php

namespace AcMarche\Api\Entity;

use AcMarche\Api\Entity\Traits\IdTrait;
use AcMarche\Api\Repository\RueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RueRepository::class)]
class Rue
{
    use IdTrait;

    #[ORM\Column(type: 'string', length: 100)]
    private string $code;
    #[ORM\Column(type: 'string', length: 200)]
    private string $nom;
    #[ORM\Column(type: 'string', length: 200)]
    private string $localite;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getLocalite(): string
    {
        return $this->localite;
    }

    public function setLocalite(string $localite): void
    {
        $this->localite = $localite;
    }
}
