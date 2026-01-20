<?php

namespace App\Models;

use App\Models\Demande;
use App\Models\Typerepondant;
use Illuminate\Database\Eloquent\Model;

class Sessionremise extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'seranneeacademique',
        'serdatedebut',
        'serdatefin',
        'seractive', 
        'anneeacademique_id', 
        'typerepondant_id'
    ];

    /**
     * The relationships.
     */

    // One-to-many : Anneeacademique  1..1 <==> 0..* Sessionremise
    public function anneeacademique()
    {
        return $this->belongsTo(Anneeacademique::class, 'anneeacademique_id', 'id');
    }

    // One-to-many : Typerepondant  1..1 <==> 0..* Sessionremise
    public function typerepondant()
    {
        return $this->belongsTo(Typerepondant::class, 'typerepondant_id', 'id');
    }

    // One-to-many : Sessionremise  1..1 <==> 0..* Demande
    public function demandes()
    {
        return $this->hasMany(Demande::class, 'sessionremise_id', 'id');
    }
}
