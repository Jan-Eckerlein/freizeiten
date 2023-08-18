<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'username',
        'email',
        'password',
        'global_role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get all of the organizations for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class);
    }

    /**
     * Get a specific organization for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function organization(string $orgId)
    {
        return $this->belongsToMany(Organization::class);
    }

    /**
     * Get the Organization that the User owns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOwnedOrganizations()
    {
        return $this->organizations()->wherePivot('org_role', 'owner')->get();
    }

    /**
     * Get all of the roles for the User for a given organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles(string $orgId)
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get the Nickname for the User for a given organization
     *
     * @return string
     */
    public function nickname(string $orgId)
    {
        return $this->belongsToMany(Organization::class)->wherePivot('organization_id', $orgId)->first()->pivot->nickname;
    }
}
