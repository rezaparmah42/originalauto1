<?php

namespace App\Models;

use App\Core\Database;

class Model
{
    protected $db;

    public function __construct()
    {
        // Ensure Database class is available; some entrypoints may skip autoloader
        if (!class_exists('\App\Core\Database')) {
            $dbFile = __DIR__ . '/../Core/Database.php';
            if (file_exists($dbFile)) {
                require_once $dbFile;
            }
        }

        $this->db = \App\Core\Database::connect();
    }
}
