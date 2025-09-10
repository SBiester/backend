<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Veraenderung extends Model
{
    protected $table = 'tbl_veraenderung';
    protected $primaryKey = 'VeraenderungID';
    public $timestamps = false;

    protected $fillable = [
        'Veraenderung_ArtID',
        'AenderungZum',
        'BefristetBis',
        'Unternehmen'
    ];

    protected $casts = [
        'AenderungZum' => 'date',
        'BefristetBis' => 'date'
    ];

    public function veraenderungArt()
    {
        return $this->belongsTo(VeraenderungArt::class, 'Veraenderung_ArtID', 'Veraenderung_ArtID');
    }

    public function auftraege()
    {
        return $this->hasMany(Auftrag::class, 'VeraenderungID', 'VeraenderungID');
    }
}