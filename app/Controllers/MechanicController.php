<?php

namespace App\Controllers;

use App\Core\Controller;

class MechanicController extends Controller
{
    public function index()
    {
        $this->view('mechanics/index');
    }
}
