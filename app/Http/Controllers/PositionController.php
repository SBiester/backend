<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positionen = ['Angestellte:r', 'Teamleiter:in', 'Abteilungsleiter:in'];
        return response()->json($positionen);
    }
}