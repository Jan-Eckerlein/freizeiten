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

    public function syncUsers(array $user_ids)
    {
        if (!in_array($this->getOwner()->id, $user_ids)) {
            throw new \Exception('Owner cannot be removed from Organization');
        }
        $this->users()->sync($user_ids);
        $this->save();
        return $this->users;
    }

    /**
     * Get the owner for the Organization
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->users()->wherePivot('org_role', 'owner')->first();
    }

    /**
     * Set the owner for the Organization
     *
     * @param User $user to set as owner
     * @return User|null $currentOwner if exists
     */
    public function setOwner(User $user)
    {
        $currentOwner = $this->getOwner();
        if ($currentOwner) {
            $this->users()->updateExistingPivot($currentOwner->id, ['org_role' => 'admin']);
        }

        $isPartOfOrg = $this->users()->where('user_id', $user->id)->exists();
        if (!$isPartOfOrg) {
            $this->users()->attach($user, ['org_role' => 'owner']);
        } else {
            $this->users()->updateExistingPivot($user->id, ['org_role' => 'owner']);
        }

        $this->save();

        return $currentOwner ?? null;
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
     * @return \Illuminate\Database\Eloquent\Collection
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

    public function removeAdmin(User $user)
    {
        $this->users()->updateExistingPivot($user->id, ['org_role' => 'member']);
        $this->save();
    }

    /**
     * Sync the admins for the Organization
     *
     * @param array $admin_ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function syncAdmins(array $admin_ids)
    {
        $oldAdmins = $this->getAdmins();
        $oldAdmins->forEach(function ($oldAdmin) {
            $this->users()->updateExistingPivot($oldAdmin->id, ['org_role' => 'member']);
        });

        collect($admin_ids)->forEach(function ($admin) {
            $this->users()->updateExistingPivot($admin->id, ['org_role' => 'admin']);
        });

        $this->save();
        return $this->getAdmins();
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
