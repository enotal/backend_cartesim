<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\Region;
use App\Models\Demande;
use App\Models\RoleUser;
use App\Models\RegionUser;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'email',
        'sexe',
        'active',
        'status',
        'password',
        'province_id',
        'region_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The relationships.
     */

    // Many-to-many : Role 1..* <===> 0..* User
    public function roles()
    {
        return $this->belongsToMany(Role::class)->using(RoleUser::class)->withPivot(['id']);
    }

    // One-to-many : User 0..1 <==> 0..* Demande
    public function demandes()
    {
        return $this->hasMany(Demande::class, 'user_id', 'id');
    }

    // One-to-many : Province 0..1 <===> 0..* User
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    // One-to-many : Region 0..1 <===> 0..* User
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }
}
