<?php

namespace AcMarche\Api\Entity;

use AcMarche\Api\Entity\Traits\IdTrait;
use AcMarche\Api\Parking\EventNotification;
use AcMarche\Api\Parking\Repository\ParkingRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParkingRepository::class)]
class Parking
{
    use IdTrait;

    #[ORM\Column(nullable: false, unique: true)]
    public int $number;
    #[ORM\Column(length: 200, nullable: false)]
    public string $name;
    #[ORM\Column(length: 200, nullable: false)]
    public string $type;
    #[ORM\Column(nullable: false)]
    public array $category;
    #[ORM\Column(length: 200, nullable: false)]
    public string $description;
    #[ORM\Column(length: 200, nullable: false)]
    public string $refParkingGroup;
    #[ORM\Column(length: 200, nullable: false)]
    public string $refParkingSite;
    #[ORM\Column(length: 200, nullable: false)]
    public string $status;
    #[ORM\Column(nullable: true)]
    public ?\DateTime $status_date;
    #[ORM\Column(nullable: false)]
    public float $latitude;
    #[ORM\Column(nullable: false)]
    public float $longitude;

    public static function createFromEvent(EventNotification $eventNotification): Parking
    {
        $parking = new self();
        $parking->number = $eventNotification->data->id;
        $parking->name = $eventNotification->data->name->value;
        $parking->type = $eventNotification->data->type;
        $parking->category = $eventNotification->data->category->value;
        $parking->description = $eventNotification->data->description->value;
        $parking->latitude = $eventNotification->data->location->value['coordinates'][0];
        $parking->longitude = $eventNotification->data->location->value['coordinates'][1];
        $parking->status = $eventNotification->data->status->value;
        try {
            $date = Carbon::create($eventNotification->data->status->metadata['timestamp']['value'])->toDateTime();
        } catch (\Exception $e) {
            $date = null;
        }
        $parking->status_date = $date;
        $parking->refParkingSite = $eventNotification->data->refParkingSite->value;
        $parking->refParkingGroup = $eventNotification->data->refParkingGroup->value;

        return $parking;
    }

    public function update(EventNotification $eventNotification): void
    {
        $this->status = $eventNotification->data->status->value;
        try {
            $date = Carbon::create($eventNotification->data->status->metadata['timestamp']['value'])->toDateTime();
        } catch (\Exception $e) {
            $date = null;
        }
        $this->status_date = $date;
    }


}