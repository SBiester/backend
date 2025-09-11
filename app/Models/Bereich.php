<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bereich extends Model
{
    protected $table = 'tbl_bereich';
    protected $primaryKey = 'BereichID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung'
    ];

    public function teams()
    {
        return $this->hasMany(Team::class, 'BereichID', 'BereichID');
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