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
        'Hersteller'
    ];

    public function referenzen()
    {
        return $this->belongsToMany(Referenz::class, 'tbl_referenz_hardware', 'HardwareID', 'ReferenzID');
    }
}