<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;

class TestController extends Controller
{

    public function store()
    {
        return view('welcome');
    }
}
