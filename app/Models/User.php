<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // The primary key for the model is set to 'id' to match the Laravel default 
    // and the structure of the old 2014 migration file.
    protected $primaryKey = 'id';
    
    // ----------------------------------------------------
    // MASS ASSIGNMENT PROTECTION
    // ----------------------------------------------------
    
    // The attributes that are mass assignable.
    protected $fillable = [
        'username', // Added for clarity and login identifier
        'email',
        'password',
        'role',     // Key column for authorization (admin/user)
        'name',     // Original column from 2014 migration
    ];

    // ----------------------------------------------------
    // HIDING ATTRIBUTES
    // ----------------------------------------------------
    
    // The attributes that should be hidden for serialization (e.g., JSON output).
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ----------------------------------------------------
    // ATTRIBUTE CASTING
    // ----------------------------------------------------

    // The attributes that should be cast to native types.
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ----------------------------------------------------
    // RELATIONSHIPS
    // ----------------------------------------------------

    /**
     * Get all assets uploaded by this user.
     * This establishes the One-to-Many relationship (User has many Assets).
     * Used in Admin View to track asset uploads.
     */
    public function assetsUploaded(): HasMany
    {
        // Parameter 1: Related model (Asset)
        // Parameter 2: Foreign Key on the Asset model ('uploadedByUserID')
        // Parameter 3: Local Key on this model ('id')
        return $this->hasMany(Asset::class, 'uploadedByUserID', 'id');
    }

    // ----------------------------------------------------
    // ACCESSORS / HELPER METHODS
    // ----------------------------------------------------

    /**
     * Checks if the user's role is 'admin'.
     * Essential for authorization logic (Admin Gate/Middleware).
     */
    public function isAdmin(): bool
    {
        // Support multiple representations of an admin role from the database:
        // - string 'admin' (case-insensitive)
        // - integer 1
        // - string '1'
        if (is_null($this->role)) {
            return false;
        }

        if (is_int($this->role)) {
            return $this->role === 1;
        }

        return strtolower((string) $this->role) === 'admin' || $this->role === '1';
    }
}