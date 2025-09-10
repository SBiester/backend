<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rollengruppe extends Model
{
    protected $table = 'tbl_rollengruppe';
    
    protected $primaryKey = 'RollengruppeID';
    
    public $timestamps = false;
    
    protected $fillable = [
        'Bezeichnung'
    ];
    
    public function sammelrollen()
    {
        return $this->hasMany(Sammelrollen::class, 'RollengruppeID', 'RollengruppeID');
    }
}