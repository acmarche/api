<?php


namespace AcMarche\Api\Entity\Traits;


use Doctrine\ORM\Mapping as ORM;

trait IdTrait
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    public function getId(): ?int
    {
        return $this->id;
    }

}
