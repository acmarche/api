<?php

namespace AcMarche\Api\Parking;

class EventNotification
{
    public readonly string $subscriptionId;
    public readonly string $tag;
    public readonly DataItem $data;

    public function __construct(string $json)
    {
        $data = json_decode($json, true);
        $this->subscriptionId = $data['subscriptionId'];
        $this->tag = $data['tag'];
        $this->data = new DataItem($data['data'][0]);
    }
}
