<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FunktionController extends Controller
{
    public function index()
    {
        $funktionen = ['Projektleiter:in', 'Entwickler:in', 'Systemadministrator:in'];
        return response()->json($funktionen);
    }
}