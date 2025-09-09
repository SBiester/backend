<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SachbereichController extends Controller
{
    public function index()
    {
        $sachbereiche = ['O-AS', 'O-IB', 'P-E', 'P-PM'];
        return response()->json($sachbereiche);
    }
}