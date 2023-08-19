<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'role';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'subject_id',
    ];

    /**
     * Get the subject that owns the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the organization that owns the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function organization()
    {
        // through table subject
        return $this->hasOneThrough(Organization::class, Subject::class);
    }

    /**
     * Get all of the users for the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
