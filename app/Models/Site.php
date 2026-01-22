<?php

namespace App\Models;

use App\Models\Demande;
use App\Models\Province;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sitlibelle',
        'sitcommentaire',
        'sitactive', 
        'province_id',
    ];

    /**
     * The relationships.
     */

    // One-to-many : Site 1..1 <==> 0..* Demande
    public function demandes()
    {
        return $this->hasMany(Demande::class, 'site_id', 'id');
    }

    // One-to-many : Province 1..1 <==> 0..* Site
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }
}
