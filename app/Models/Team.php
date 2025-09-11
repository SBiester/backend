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
        'BereichID'
    ];

    public function bereich()
    {
        return $this->belongsTo(Bereich::class, 'BereichID', 'BereichID');
    }

    public function funktionen()
    {
        return $this->hasMany(Funktion::class, 'TeamID', 'TeamID');
    }
}