<?php

namespace App\Models;

use App\Models\Region;
use App\Models\Demande;
use App\Models\Province;
use App\Models\Anneeacademique;
use Illuminate\Database\Eloquent\Model;

class Sim extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'simnumero',
        'simcode',
        'simdateactivation',
        'simdateremise',
        'simdatesuspension',
        'simdateretrait',
        'simperdue',
        'simcommentaire',
        'simdeclarationperte',
        'anneeacademique_id',
        'demande_id',
        'province_id',
        'region_id',
    ];

    /**
     * The relationships.
     */

    // One-to-many : Anneeacademique 1..1 <==> 0..* Sim
    public function anneeacademique()
    {
        return $this->belongsTo(Anneeacademique::class, 'anneeacademique_id', 'id');
    }

    // One-to-many : Sim 0..1 <==> 0..1 Demande
    public function sessiondemande()
    {
        return $this->belongsTo(Sessiondemande::class, 'sessiondemande_id', 'id');
    }

    // One-to-many : Sessionremise 1..1 <==> 0..* Demande
    public function sessionremise()
    {
        return $this->belongsTo(Sessionremise::class, 'sessionremise_id', 'id');
    }

    // One-to-many : Sim 0..1 <==> 0..1 Demande
    public function demande()
    {
        return $this->belongsTo(Demande::class, 'demande_id', 'id');
    }

    // One-to-many : Province 0..1 <==> 0..* Sim
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    // One-to-many : Region 0..1 <==> 0..* Sim
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }
}
