<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = ['PM', 'E'];
        return response()->json($teams);
    }
}