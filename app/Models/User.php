<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute; // <-- Add this
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Added a constant for the admin role for consistency
    const ROLE_ADMIN = 'admin';
    const ROLE_VET = 'vet';
    const ROLE_RECEPTIONIST = 'receptionist';
    // Changed this to match what's used in your forms
    const ROLE_STAFF = 'staff'; 


    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * NEW: Accessor for getting the role's display name. ✨
     */
    protected function roleName(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->role) {
                self::ROLE_ADMIN => 'Administrator',
                self::ROLE_VET => 'Veterinarian',
                self::ROLE_RECEPTIONIST => 'Receptionist',
                self::ROLE_STAFF => 'Assistant Staff',
                default => ucfirst($this->role),
            },
        );
    }

    // Helper functions to check roles
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isVet()
    {
        return $this->hasRole(self::ROLE_VET);
    }
    
    public function isReceptionist()
    {
        return $this->hasRole(self::ROLE_RECEPTIONIST);
    }

    // REMOVED: The index() method does not belong in the User model.
    // It should be in your UserController.php file.
}