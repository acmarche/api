<?php

namespace AcMarche\Api\Parking;

class CommuniThingsAPI
{
    private $baseUrl;
    private $token;

    public function __construct($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Login to the API and retrieve the token
     */
    public function login($email, $password)
    {
        $url = $this->baseUrl.'/api/authenticate';
        $payload = [
            'email' => $email,
            'password' => $password,
        ];

        $response = $this->sendRequest('POST', $url, $payload);
        if (isset($response['token'])) {
            $this->token = $response['token'];

            return $this->token;
        }

        throw new \Exception('Login failed: '.json_encode($response));
    }

    /**
     * Subscribe to parking events
     */
    public function subscribe($clientID, $callbackURL, $deploymentID, $options = [])
    {
        $url = $this->baseUrl.'/api/ParkingEventSubscription';
        $payload = array_merge([
            'clientID' => $clientID,
            'callbackURL' => $callbackURL,
            'deploymentID' => $deploymentID,
        ], $options);

        return $this->sendRequest('POST', $url, $payload, true);
    }

    /**
     * List all subscriptions
     */
    public function listSubscriptions($clientID)
    {
        $url = $this->baseUrl.'/api/parkingeventdatasubscription/list';
        $payload = ['clientID' => $clientID];

        return $this->sendRequest('POST', $url, $payload, true);
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription($subscriptionID)
    {
        $url = $this->baseUrl.'/api/ParkingEventSubscriptionStop';
        $payload = ['subscriptionID' => $subscriptionID];

        return $this->sendRequest('POST', $url, $payload, true);
    }

    /**
     * Delete all subscriptions
     */
    public function deleteAllSubscriptions($clientID)
    {
        $url = $this->baseUrl.'/api/parkingeventdatasubscription/delete';
        $payload = ['clientID' => $clientID];

        return $this->sendRequest('POST', $url, $payload, true);
    }

    /**
     * Send HTTP requests
     */
    private function sendRequest($method, $url, $payload = [], $auth = false)
    {
        $ch = curl_init();
        $headers = ['Content-Type: application/json'];
        if ($auth && $this->token) {
            $headers[] = 'Authorization: Bearer '.$this->token;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300) {
            return $result;
        }

        throw new \Exception('HTTP Error: '.$httpCode.' - '.json_encode($result));
    }
}
