<?php

namespace App\Http\Controllers;

class LandingController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('home');
        }

        return view('landing');
    }
}
