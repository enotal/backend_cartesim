<?php

namespace App\Models;

use App\Models\Sim;
use App\Models\Sessionremise;
use App\Models\Sessiondemande;
use Illuminate\Database\Eloquent\Model;

class Anneeacademique extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'acacode',
        'acadatedebut',
        'acadatefin',
        'acaactive', 
        'acacommentaire'
    ];

    /**
     * The relationships.
     */

    // One-to-many : Anneeacademique  1..1 <==> 0..* Sessiondemande
    public function sessiondemandes()
    {
        return $this->hasMany(Sessiondemande::class, 'anneeacademique_id', 'id');
    }

    // One-to-many : Anneeacademique  1..1 <==> 0..* Sessionremise
    public function sessionremises()
    {
        return $this->hasMany(Sessionremise::class, 'anneeacademique_id', 'id');
    }

    // One-to-many : Anneeacademique 1..1 <==> 0..* Sim
    public function sims()
    {
        return $this->hasMany(Sim::class, 'anneeacademique_id', 'id');
    }
}
