<?php
namespace App\Core;

abstract class Constants{
    public const SHIPPING_COST = 15;
    public const TPS = 0.05;
    public const TVQ = 0.09975;
    public const STRIPE_FORMAT = 100;

    public const orderState = [
        "preparing"=>"Preparing",
        "sent"=>"Sent",
        "transit"=>"In transit",
        "delivered"=>"Delivered",
    ];
    
}