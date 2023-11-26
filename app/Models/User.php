<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    public function getCreatedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'tg_name',
        'email',
        'password',
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
    ];

    public function hasAnyRole(mixed $roles): bool
    {
        $rolesOfThisUser = $this->roles->map->title->toArray();
        if (! is_array($roles)) {
            $roles = [$roles];
        }

        $rolesOfThisUser = array_map('mb_strtolower', $rolesOfThisUser);
        $roles = array_map('mb_strtolower', $roles);

        foreach ($roles as $role) {
            if (in_array($role, $rolesOfThisUser)) {
                return true;
            }
        }

        return false;
    }
}
