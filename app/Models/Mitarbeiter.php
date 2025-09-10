<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mitarbeiter extends Model
{
    protected $table = 'tbl_ma';
    protected $primaryKey = 'MAID';
    public $timestamps = false;

    protected $fillable = [
        'MA_Nummer',
        'Vorname',
        'Name',
        'Funktion',
        'Vorgesetzter',
        'MA_TypID',
        'BereichID',
        'PositionID'
    ];

    public function mitarbeiterTyp()
    {
        return $this->belongsTo(MitarbeiterTyp::class, 'MA_TypID', 'MA_TypID');
    }

    public function bereich()
    {
        return $this->belongsTo(Bereich::class, 'BereichID', 'BereichID');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'PositionID', 'PositionID');
    }

    public function auftraege()
    {
        return $this->hasMany(Auftrag::class, 'MAID', 'MAID');
    }

    public function getFullNameAttribute()
    {
        return $this->Vorname . ' ' . $this->Name;
    }
}