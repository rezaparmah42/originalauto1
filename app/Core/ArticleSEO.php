<?php

namespace App\Core;

class ArticleSEO
{
    public static function schema($title)
    {
        return [
            '@type'=>'Article',
            'headline'=>$title
        ];
    }
}
