<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategorie extends Model
{
    protected $table = 'tbl_kategorie';
    protected $primaryKey = 'KategorieID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung'
    ];

    public function software()
    {
        return $this->hasMany(Software::class, 'KategorieID', 'KategorieID');
    }
}