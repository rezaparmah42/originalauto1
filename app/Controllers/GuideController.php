<?php

namespace App\Controllers;

use App\Core\Controller;

class GuideController extends Controller
{
    public function index()
    {
        $this->view('maintenance-guides/index');
    }
}
