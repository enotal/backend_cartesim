<?php

namespace App\Models;

use App\Models\Repondant;
use App\Models\Sessionremise;
use App\Models\Sessiondemande;
use Illuminate\Database\Eloquent\Model;

class Typerepondant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tyrcode', 
        'tyrlibelle', 
        'tyractive', 
    ];

    /**
     * The relationships.
     */

    // One-to-many : Typerepondant 1..1 <==> 0..* Repondant
    public function repondants()
    {
        return $this->hasMany(Repondant::class, 'typerepondant_id', 'id'); 
    } 

    // One-to-many : Typerepondant  1..1 <==> 0..* Sessiondemande
    public function sessiondemandes()
    {
        return $this->hasMany(Sessiondemande::class, 'typerepondant_id', 'id'); 
    }

    // One-to-many : Typerepondant  1..1 <==> 0..* Sessionremise
    public function sessionremises()
    {
        return $this->hasMany(Sessionremise::class, 'typerepondant_id', 'id'); 
    }
}
