<?php

namespace App\Core;

class Security
{
    public static function clean($value)
    {
        return htmlspecialchars(
            $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}
