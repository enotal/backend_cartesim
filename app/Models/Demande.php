<?php

namespace App\Models;

use App\Models\Sim;
use App\Models\Site;
use App\Models\User;
use App\Models\Repondant;
use App\Models\Sessionremise;
use App\Models\Sessiondemande;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'dmdcode',
        'dmddate',
        'dmdcommentaire',
        'repondant_id',
        'sessiondemande_id',
        'sessionremise_id',
        'site_id', 
        'user_id'
    ];

    /**
     * The relationships.
     */

    // One-to-many : Repondant 1..1 <==> 0..* Sessiondemande
    public function repondant()
    {
        return $this->belongsTo(Repondant::class, 'repondant_id', 'id');
    }

    // One-to-many : Sessiondemande 1..1 <==> 0..* Demande
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
    public function sim()
    {
        return $this->hasOne(Sim::class, 'demande_id', 'id');
    }

    // One-to-many : Site 1..1 <==> 0..* Demande
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id', 'id');
    }

    // One-to-many : User 0..1 <==> 0..* Demande
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
