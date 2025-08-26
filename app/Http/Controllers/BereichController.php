<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BereichController extends Controller
{
    public function index()
    {
        $bereiche = ['TK', 'IT', 'PM', 'MK'];
        return response()->json($bereiche);
    }
}