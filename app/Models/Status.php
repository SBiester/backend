<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'tbl_status';
    protected $primaryKey = 'StatusID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung'
    ];

    public function auftraege()
    {
        return $this->hasMany(Auftrag::class, 'StatusID', 'StatusID');
    }
}