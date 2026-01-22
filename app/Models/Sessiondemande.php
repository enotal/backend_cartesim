<?php

namespace App\Models;

use App\Models\Demande;
use App\Models\Typerepondant;
use App\Models\Anneeacademique;
use Illuminate\Database\Eloquent\Model;

class Sessiondemande extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sedanneeacademique',
        'seddatedebut',
        'seddatefin',
        'sedactive',
        'anneeacademique_id',
        'typerepondant_id'
    ];

    /**
     * The relationships.
     */

    // One-to-many : Anneeacademique  1..1 <==> 0..* Sessiondemande
    public function anneeacademique()
    {
        return $this->belongsTo(Anneeacademique::class, 'anneeacademique_id', 'id');
    }

    // One-to-many : Typerepondant  1..1 <==> 0..* Sessiondemande
    public function typerepondant()
    {
        return $this->belongsTo(Typerepondant::class, 'typerepondant_id', 'id');
    }

    // One-to-many : Sessiondemande  1..1 <==> 0..* Demande
    public function demandes()
    {
        return $this->hasMany(Demande::class, 'sessiondemande_id', 'id');
    }
}
