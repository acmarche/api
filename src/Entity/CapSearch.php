<?php


namespace AcMarche\Api\Entity;

use AcMarche\Api\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Api\Repository\CapSearchRepository")
 */
class CapSearch implements TimestampableInterface
{
    use IdTrait,
        TimestampableTrait;

    /**
     * @ORM\Column(type="string", length=200, nullable=false)
     */
    private $keyword;

    public function __construct(string $keyword)
    {
        $this->keyword = $keyword;
    }


}
