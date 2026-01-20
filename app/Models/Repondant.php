<?php

namespace App\Models;

use App\Models\Demande;
use App\Models\Typerepondant;
use Illuminate\Database\Eloquent\Model;

class Repondant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'repidentifiant',
        'repsexe',
        'repemail',
        'repactive',
        'typerepondant_id'
    ];

    /**
     * The relationships.
     */

    // One-to-many : Typerepondant 1..1 <==> 0..* Repondant
    public function typerepondant()
    {
        return $this->belongsTo(Typerepondant::class, 'typerepondant_id', 'id');
    }

    // One-to-many : Repondant  1..1 <==> 0..* Demande
    public function demandes()
    {
        return $this->hasMany(Demande::class, 'repondant_id', 'id');
    }
}
