<?php

namespace AcMarche\Api\Parking;

use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationHandler
{

    /**
     * Process incoming POST requests from CommuniThings
     */
    public function handleRequest(): JsonResponse
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON input'], 400);
        }

        if (!isset($data['tag'])) {
            return new JsonResponse(['error' => 'Missing tag in request'], 400);
        }

        switch ($data['tag']) {
            case 'SubscriptionNotification':
                return $this->handleSubscriptionResult($data);

            case 'EventNotification':
                return $this->handleEventNotification($data);

            case 'ParkingUpdate':
                $this->handleParkingUpdate($data);

            case 'ClusterUpdate':
            case 'ClusterCreate':
            case 'ClusterDelete':
                return $this->handleClusterUpdate($data);

            default:
                return new JsonResponse(['error' => 'Unknown tag: '.$data['tag']], 400);
        }
    }

    private function handleSubscriptionResult($data): JsonResponse
    {
        // Handle subscription results (list of parking spots or clusters)
        $subscriptionId = $data['subscriptionId'] ?? 'Unknown';
        $details = $data['data'] ?? [];

        // Log or process subscription details
        $this->logData('Subscription Result', ['subscriptionId' => $subscriptionId, 'data' => $details]);

        return new JsonResponse(['status' => 'Subscription Result processed'], 200);
    }

    private function handleEventNotification($data): JsonResponse
    {
        // Handle parking event notifications
        $subscriptionId = $data['subscriptionId'] ?? 'Unknown';
        $events = $data['data'] ?? [];

        // Log or process event details
        $this->logData('Parking Event Notification', ['subscriptionId' => $subscriptionId, 'events' => $events]);

        return new JsonResponse(['status' => 'Event Notification processed'], 200);
    }

    private function handleParkingUpdate($data): JsonResponse
    {
        // Handle parking updates (additions, updates, or removals)
        $subscriptionId = $data['subscriptionId'] ?? 'Unknown';
        $updates = $data['data'] ?? [];

        // Log or process parking updates
        $this->logData('Parking Update', ['subscriptionId' => $subscriptionId, 'updates' => $updates]);

        return new JsonResponse(['status' => 'Subscription Result processed'], 200);
    }

    private function handleClusterUpdate($data): JsonResponse
    {
        // Handle cluster updates (create, update, delete)
        $subscriptionId = $data['subscriptionId'] ?? 'Unknown';
        $clusterDetails = $data['data'] ?? [];

        // Log or process cluster details
        $this->logData('Cluster Update', ['subscriptionId' => $subscriptionId, 'details' => $clusterDetails]);

        return new JsonResponse(['status' => 'Cluster Update processed'], 200);
    }

    private function logData($context, $data): void
    {
        $log = sprintf("[%s] %s: %s\n", date('Y-m-d H:i:s'), $context, json_encode($data));
        file_put_contents(__DIR__.'/notifications.log', $log, FILE_APPEND);
    }
}