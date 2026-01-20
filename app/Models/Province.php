<?php

namespace App\Models;

use App\Models\Sim;
use App\Models\Site;
use App\Models\User;
use App\Models\Region;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'prvcode',
        'prvnom',
        'prvcheflieu',
        'prvactive',
        'prvcommentaire',
        'region_id',
    ];

    /**
     * The relationships.
     */

    // One-to-many : Region 1..1 <==> 0..* Province
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    // One-to-many : Province 1..1 <==> 0..* Site
    public function sites()
    {
        return $this->hasMany(Site::class, 'province_id', 'id');
    }

    // One-to-many : Province 0..1 <==> 0..* Sim
    public function sims()
    {
        return $this->hasMany(Sim::class, 'province_id', 'id');
    }

    // One-to-many : Province 0..1 <===> 0..* User
    public function users()
    {
        return $this->hasMany(User::class, 'province_id', 'id');
    }
}
