<?php

namespace AcMarche\Api\Parking;

class DataItem {
    public readonly int $id;
    public readonly string $type;
    public readonly Attribute $category;
    public readonly Attribute $dateModified;
    public readonly Attribute $description;
    public readonly Location $location;
    public readonly Attribute $name;
    public readonly Attribute $refDevice;
    public readonly Attribute$refParkingGroup;
    public readonly Attribute$refParkingSite;
    public readonly Attribute$status;
    public readonly Attribute $session;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->type = $data['type'];
        $this->category = new Attribute($data['category']);
        $this->dateModified = new Attribute($data['dateModified']);
        $this->description = new Attribute($data['description']);
        $this->location = new Location($data['location']);
        $this->name = new Attribute($data['name']);
        $this->refDevice = new Attribute($data['refDevice']);
        $this->refParkingGroup = new Attribute($data['refParkingGroup']);
        $this->refParkingSite = new Attribute($data['refParkingSite']);
        $this->status = new Attribute($data['status']);
        $this->session = new Attribute($data['session']);
    }}