<?php

namespace App\Models;

use App\Models\Sim;
use App\Models\User;
use App\Models\Province;
use App\Models\RegionUser;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rgncode',
        'rgnordre',
        'rgnnom',
        'rgncheflieu',
        'rgnactive',
        'rgncommentaire',
    ];

    /**
     * The relationships.
     */

    // One-to-many : Region 1..1 <==> 0..* Province
    public function provinces()
    {
        return $this->hasMany(Province::class, 'region_id', 'id');
    }

    // One-to-many : Region 0..1 <==> 0..* Sim
    public function sims()
    {
        return $this->hasMany(Sim::class, 'region_id', 'id');
    }

    // One-to-many : Region 0..1 <===> 0..* User
    public function users()
    {
        return $this->hasMany(User::class, 'region_id', 'id');
    }
}
