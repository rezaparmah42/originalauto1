<?php

namespace App\Core;

class SEO
{
    public static function title($title)
    {
        return $title . ' | ' . SITE_NAME;
    }

    public static function description($text)
    {
        return $text;
    }
}
