<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sammelrollen extends Model
{
    protected $table = 'tbl_sammelrollen';
    
    protected $primaryKey = 'SammelrollenID';
    
    public $timestamps = false;
    
    protected $fillable = [
        'Bezeichnung',
        'Schluessel',
        'RollengruppeID'
    ];
    
    public function rollengruppe()
    {
        return $this->belongsTo(Rollengruppe::class, 'RollengruppeID', 'RollengruppeID');
    }
}