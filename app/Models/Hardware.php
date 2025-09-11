<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hardware extends Model
{
    protected $table = 'tbl_hardware';
    protected $primaryKey = 'HardwareID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung',
        'KategorieID'
    ];

    public function kategorie()
    {
        return $this->belongsTo(Kategorie::class, 'KategorieID', 'KategorieID');
    }
    
    public function referenzen()
    {
        return $this->belongsToMany(Referenz::class, 'tbl_referenz_hardware', 'HardwareID', 'ReferenzID');
    }
}