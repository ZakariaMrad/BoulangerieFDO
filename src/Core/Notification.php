<?php
namespace App\Core;

class Notification
{
    private $content;
    private $color;

    public function __construct($content, $color = NotificationColors::ADD)
    {
        $this->content = $content;
        $this->color = $color;
    }
    public function getContent() 
    {
        return $this->content;
    }

    public function getColor() 
    {
        return $this->color;
    }
}

abstract class NotificationColors {
    public const ADD = "cart-notification-add";
    public const UPDATE = "cart-notification-update";
    public const REMOVE = "cart-notification-remove";
    public const ERROR = "cart-notification-error";
}

