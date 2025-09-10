<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bereich extends Model
{
    protected $table = 'tbl_bereich';
    protected $primaryKey = 'BereichID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung',
        'TeamID'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'TeamID', 'TeamID');
    }

    public function referenzen()
    {
        return $this->hasMany(Referenz::class, 'BereichID', 'BereichID');
    }

    public function mitarbeiter()
    {
        return $this->hasMany(Mitarbeiter::class, 'BereichID', 'BereichID');
    }
}