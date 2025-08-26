<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VorgesetzteController extends Controller
{
    public function index()
    {
        $vorgesetzte = ['Wehmann Frank', 'Baumgart Christian', 'Mustermann Max', 'Musterfrau Erika'];
        return response()->json($vorgesetzte);
    }
}