<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
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
        ];
    }

    /**
     * Hash the password only when a value is actually provided.
     *
     * Users who sign in with Google do not have a local password.
     * Passing null to the default "hashed" cast would still store
     * a hash of an empty value, which is not what we want. This
     * accessor lets the password column stay truly null for those
     * accounts, while still hashing any real password on the way in.
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if ($value === null) {
                    return null;
                }

                return \Illuminate\Support\Facades\Hash::make($value);
            },
        );
    }

    /**
     * Whether this user has a local password.
     *
     * A user created through Google OAuth has no password. A user
     * created through the normal registration flow has one. This
     * helper is useful when deciding whether to offer password
     * reset or password change options in the UI.
     */
    public function hasPassword(): bool
    {
        return $this->password !== null;
    }

    /**
     * Every user owns zero or more parcels.
     *
     * If the user is deleted, their parcels are deleted with them
     * because the parcels.user_id foreign key uses cascadeOnDelete.
     */
    public function parcels(): HasMany
    {
        return $this->hasMany(Parcel::class);
    }
}