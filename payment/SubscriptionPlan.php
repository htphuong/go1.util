<?php

namespace go1\util\payment;

use stdClass;

class SubscriptionPlan
{
    public $uuid;
    public $instance;
    public $price;
    public $currency;
    public $interval;
    public $count;
    public $trialPeriodDays = 0;
    public $data;

    private function __construct()
    {
        # The object should not be created manually.
    }

    public static function create(stdClass $row)
    {
        $plan = new SubscriptionPlan;
        $plan->uuid = $row->uuid ?? null;
        $plan->instance = $row->instance;

        return $plan;
    }
}
