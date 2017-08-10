<?php

namespace go1\util\payment;

use go1\util\model\User;
use GuzzleHttp\Client;

class SubscriptionClient
{
    private $client;
    private $subscriptionUrl;

    public function __construct(Client $client, string $subscriptionUrl)
    {
        $this->client = $client;
        $this->subscriptionUrl = $subscriptionUrl;
    }

    public function createPlan(SubscriptionPlan $plan)
    {
        # …
    }

    public function updatePlan(SubscriptionPlan $plan)
    {
        # …
    }

    public function subscribe(User $user, SubscriptionPlan $plan)
    {

    }

    public function unsubscribe(User $user, SubscriptionPlan $plan)
    {
    }
}
