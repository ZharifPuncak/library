<?php

namespace App\Http\Controllers;

class VrController extends Controller
{
    public function index()
    {
        return view('vr.index');
    }

    public function health()
    {
        return response('VR Route is accessible!');
    }
}
