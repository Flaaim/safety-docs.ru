<?php

namespace Test\Functional;



use YooKassa\Client;
use YooKassa\Request\Payments\PaymentsResponse;

class YookassaClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuth('221345', 'test_0B3flJqsbdKNA2sS2dT0ahs74LtF7fwJq2oVR-8wTCM');
    }
    public function getPayments(): PaymentsResponse
    {
        return $this->client->getPayments();
    }
    public function getLastPayment(): ?string
    {
        if(count($payments = $this->getPayments()->getItems()->toArray()) > 0) {
            return $payments[0]['id'];
        }
        return null;
    }
}