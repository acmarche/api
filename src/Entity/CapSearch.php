<?php


namespace AcMarche\Api\Entity;

use AcMarche\Api\Repository\CapSearchRepository;
use AcMarche\Api\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

#[ORM\Entity(repositoryClass: CapSearchRepository::class)]
class CapSearch implements TimestampableInterface
{
    use IdTrait,
        TimestampableTrait;

    #[ORM\Column(type: 'string', length: 200, nullable: false)]
    private string $keyword;

    public function __construct(string $keyword)
    {
        $this->keyword = $keyword;
    }
}
