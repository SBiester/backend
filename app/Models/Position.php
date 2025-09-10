<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table = 'tbl_position';
    protected $primaryKey = 'PositionID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung'
    ];

    public function mitarbeiter()
    {
        return $this->hasMany(Mitarbeiter::class, 'PositionID', 'PositionID');
    }
}