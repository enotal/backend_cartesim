<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'prmlibelle',
        'prmlien',
        'prmdescription',
        'prmactive',
    ];

    /**
     * The relationships.
     */
}
