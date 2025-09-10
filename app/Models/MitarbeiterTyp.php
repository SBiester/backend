<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MitarbeiterTyp extends Model
{
    protected $table = 'tbl_ma_typ';
    protected $primaryKey = 'MA_TypID';
    public $timestamps = false;

    protected $fillable = [
        'Bezeichnung'
    ];

    public function mitarbeiter()
    {
        return $this->hasMany(Mitarbeiter::class, 'MA_TypID', 'MA_TypID');
    }
}