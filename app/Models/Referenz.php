<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referenz extends Model
{
    protected $table = 'tbl_referenz';
    protected $primaryKey = 'ReferenzID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung',
        'BereichID',
        'aktiv'
    ];
    
    // Allow null values for BereichID
    protected $attributes = [
        'aktiv' => true
    ];

    protected $casts = [
        'aktiv' => 'boolean'
    ];

    public function bereich()
    {
        return $this->belongsTo(Bereich::class, 'BereichID', 'BereichID')->withDefault([
            'Bezeichnung' => 'Kein Bereich zugeordnet'
        ]);
    }

    public function software()
    {
        return $this->belongsToMany(Software::class, 'tbl_referenz_software', 'ReferenzID', 'SoftwareID');
    }

    public function hardware()
    {
        return $this->belongsToMany(Hardware::class, 'tbl_referenz_hardware', 'ReferenzID', 'HardwareID');
    }

    public function sammelrollen()
    {
        return $this->belongsToMany(Sammelrollen::class, 'tbl_sammelrollen_referenz', 'ReferenzID', 'SammelrollenID');
    }

    public function auftraege()
    {
        return $this->belongsToMany(Auftrag::class, 'tbl_auftrag_referenz', 'ReferenzID', 'AuftragID');
    }
}