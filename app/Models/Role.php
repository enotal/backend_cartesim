<?php

namespace App\Models;

use App\Models\User;
use App\Models\RoleUser;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rlelibelle',
        'rledescription',
        'relactive',
    ];

    /**
     * The relationships.
     */

    // Many-to-many : Role 1..* <===> 0..* User
    public function users()
    {
        return $this->belongsToMany(User::class)->using(RoleUser::class)->withPivot(['id']);
    }

}
