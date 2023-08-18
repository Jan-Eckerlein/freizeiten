<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'organization';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get all of the users for the Organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('nickname', 'org_role');
    }

    /**
     * Get the owner for the Organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getOwner()
    {
        return $this->users()->wherePivot('org_role', 'owner')->get();
    }

    /**
     * Set the owner for the Organization
     *
     * @param User $user
     */
    public function setOwner(User $user)
    {
        $this->users()->attach($user, ['org_role' => 'owner']);
        $this->save();
    }

    /**
     * Get all of the members for the Organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getMembers()
    {
        return $this->users()->wherePivot('org_role', 'member')->get();
    }

    /**
     * Add a member to the Organization
     *
     * @param User $user
     * @param string $nickname
     */
    public function addMember(User $user, string $nickname)
    {
        $this->users()->attach($user, ['org_role' => 'member', 'nickname' => $nickname]);
        $this->save();
    }

    /**
     * Get all of the admins for the Organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getAdmins()
    {
        return $this->users()->wherePivot('org_role', 'admin')->get();
    }

    /**
     * Add an admin to the Organization
     *
     * @param User $user
     * @param string $nickname
     */
    public function addAdmin(User $user, string $nickname)
    {
        $this->users()->attach($user, ['org_role' => 'admin', 'nickname' => $nickname]);
        $this->save();
    }

    /**
     * Get all of the roles for the Organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
