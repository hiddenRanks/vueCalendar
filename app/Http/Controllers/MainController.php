<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(Request $req)
    {
        return view('index');
    }

    public function getData(Request $req)
    {

    }
}
