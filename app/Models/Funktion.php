<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funktion extends Model
{
    protected $table = 'tbl_funktion';
    protected $primaryKey = 'FunktionID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung',
        'TeamID'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'TeamID', 'TeamID');
    }
}