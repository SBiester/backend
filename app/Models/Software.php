<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Software extends Model
{
    protected $table = 'tbl_software';
    protected $primaryKey = 'SoftwareID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung',
        'HerstellerID',
        'Sammelrollen',
        'aktiv'
    ];

    protected $casts = [
        'Sammelrollen' => 'boolean',
        'aktiv' => 'boolean'
    ];

    public function hersteller()
    {
        return $this->belongsTo(Hersteller::class, 'HerstellerID', 'HerstellerID');
    }


    public function rollengruppen()
    {
        return $this->belongsToMany(Rollengruppe::class, 'tbl_software_rollengruppe', 'SoftwareID', 'RollengruppeID');
    }

    public function referenzen()
    {
        return $this->belongsToMany(Referenz::class, 'tbl_referenz_software', 'SoftwareID', 'ReferenzID');
    }
}