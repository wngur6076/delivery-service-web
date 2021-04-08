<?php

namespace App\Billing;

interface PaymentGateway
{
    public function charge($amount);

    public function getValidTestToken();
}
