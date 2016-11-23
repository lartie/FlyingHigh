<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class WelcomeController extends Controller
{
    public function index()
    {
        return view('index');
    }
}
