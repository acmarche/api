<?php

namespace AcMarche\Api\Entity;

use AcMarche\Api\Entity\Traits\IdTrait;
use AcMarche\Api\Parking\Repository\ParkingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParkingRepository::class)]
class AccessToken
{
    use IdTrait;

    #[ORM\Column(nullable: false, unique: true)]
    public string $token = '';

    #[ORM\ManyToOne(targetEntity: User::class)]
    public ?User $user = null;

    public function isValid(): bool
    {
        return $this->user !== null;
    }

}