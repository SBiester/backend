<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SachbereichController extends Controller
{
    public function index()
    {
        $sachbereiche = ['Anwendungsentwicklung', 'Systemintegration', 'Datenanalyse'];
        return response()->json($sachbereiche);
    }
}