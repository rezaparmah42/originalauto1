<?php

namespace App\Core;

class Controller
{
    protected function view($file, $data = [])
    {
        extract($data);
        require __DIR__ . '/../Views/' . $file . '.php';
    }
}
