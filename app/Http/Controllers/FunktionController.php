<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FunktionController extends Controller
{
    public function index()
    {
        $funktionen = ['Projektleiter', 'Entwickler', 'Systemadministrator', 'UI/UX Designer'];
        return response()->json($funktionen);
    }
}