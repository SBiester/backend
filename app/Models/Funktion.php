<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funktion extends Model
{
    protected $table = 'tbl_funktion';
    protected $primaryKey = 'FunktionID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung'
    ];

    public function teams()
    {
        return $this->hasMany(Team::class, 'FunktionID', 'FunktionID');
    }
}