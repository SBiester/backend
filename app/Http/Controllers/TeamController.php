<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = ['Team Alpha', 'Team Bravo', 'Team Charlie'];
        return response()->json($teams);
    }
}