<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VorgesetzteController extends Controller
{
    public function index()
    {
        $vorgesetzte = ['Wehmann Frank', 'Baumgart Christian', 'Riedel Carsten'];
        return response()->json($vorgesetzte);
    }
}