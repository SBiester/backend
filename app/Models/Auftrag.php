<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auftrag extends Model
{
    protected $table = 'tbl_auftrag';
    protected $primaryKey = 'AuftragID';
    public $timestamps = false;

    protected $fillable = [
        'VeraenderungID',
        'MAID',
        'AuftragDatum',
        'AuftragMA',
        'StatusID',
        'Kommentar'
    ];

    protected $casts = [
        'AuftragDatum' => 'date'
    ];

    public function veraenderung()
    {
        return $this->belongsTo(Veraenderung::class, 'VeraenderungID', 'VeraenderungID');
    }

    public function mitarbeiter()
    {
        return $this->belongsTo(Mitarbeiter::class, 'MAID', 'MAID');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'StatusID', 'StatusID');
    }

    public function referenzen()
    {
        return $this->belongsToMany(Referenz::class, 'tbl_auftrag_referenz', 'AuftragID', 'ReferenzID');
    }

    public function elemente()
    {
        return $this->belongsToMany(Element::class, 'tbl_auftrag_element', 'AuftragID', 'ElementID');
    }

    // Scope for open orders (not completed or cancelled)
    public function scopeOpen($query)
    {
        return $query->whereHas('status', function($q) {
            $q->whereNotIn('Bezeichnung', ['completed', 'cancelled', 'abgeschlossen', 'abgebrochen']);
        });
    }

    // Scope for user orders by employee email
    public function scopeForUser($query, $userEmail)
    {
        return $query->whereHas('mitarbeiter', function($q) use ($userEmail) {
            // Assuming email is derived from name or stored separately
            $q->where('Name', 'like', '%' . explode('@', $userEmail)[0] . '%');
        });
    }
}