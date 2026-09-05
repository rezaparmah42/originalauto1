<?php

namespace App\Controllers;

use App\Core\Controller;

class CommonProblemController extends Controller
{
    public function index()
    {
        $this->view('common-problems/index');
    }
}
