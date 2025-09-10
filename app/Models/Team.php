<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'tbl_team';
    protected $primaryKey = 'TeamID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung',
        'FunktionID'
    ];

    public function funktion()
    {
        return $this->belongsTo(Funktion::class, 'FunktionID', 'FunktionID');
    }

    public function bereiche()
    {
        return $this->hasMany(Bereich::class, 'TeamID', 'TeamID');
    }
}