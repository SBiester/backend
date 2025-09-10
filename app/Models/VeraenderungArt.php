<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VeraenderungArt extends Model
{
    protected $table = 'tbl_veraenderung_art';
    protected $primaryKey = 'Veraenderung_ArtID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung'
    ];

    public function veraenderungen()
    {
        return $this->hasMany(Veraenderung::class, 'Veraenderung_ArtID', 'Veraenderung_ArtID');
    }
}