<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hersteller extends Model
{
    protected $table = 'tbl_hersteller';
    protected $primaryKey = 'HerstellerID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung'
    ];

    public function software()
    {
        return $this->hasMany(Software::class, 'HerstellerID', 'HerstellerID');
    }
}