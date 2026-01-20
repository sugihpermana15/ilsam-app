<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        return view('pages.aboutus.company');
    }

    public function ceo()
    {
        return view('pages.aboutus.ceo');
    }

    public function philosophy()
    {
        return view('pages.aboutus.philosophy');
    }
}
