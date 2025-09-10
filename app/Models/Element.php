<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    protected $table = 'tbl_element';
    protected $primaryKey = 'ElementID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung',
        'SoftwareID',
        'HardwareID',
        'SammelrollenID'
    ];

    public function software()
    {
        return $this->belongsTo(Software::class, 'SoftwareID', 'SoftwareID');
    }

    public function hardware()
    {
        return $this->belongsTo(Hardware::class, 'HardwareID', 'HardwareID');
    }

    public function sammelrollen()
    {
        return $this->belongsTo(Sammelrollen::class, 'SammelrollenID', 'SammelrollenID');
    }

    public function auftraege()
    {
        return $this->belongsToMany(Auftrag::class, 'tbl_auftrag_element', 'ElementID', 'AuftragID');
    }

    // Get the actual item (software, hardware, or sammelrollen)
    public function getItemAttribute()
    {
        if ($this->SoftwareID) {
            return $this->software;
        } elseif ($this->HardwareID) {
            return $this->hardware;
        } elseif ($this->SammelrollenID) {
            return $this->sammelrollen;
        }
        return null;
    }

    public function getTypeAttribute()
    {
        if ($this->SoftwareID) {
            return 'software';
        } elseif ($this->HardwareID) {
            return 'hardware';
        } elseif ($this->SammelrollenID) {
            return 'sammelrollen';
        }
        return null;
    }
}