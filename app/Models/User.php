<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'email',
        'password',
        'role',
        'telephone',
        'adresse',
        'actif',
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
            'actif' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin or director.
     */
    public function isAdminOrDirector()
    {
        return in_array($this->role, ['administrateur', 'directeur']);
    }

    /**
     * Get role options for forms
     */
    public static function getRoleOptions()
    {
        return [
            'administrateur' => 'Administrateur',
            'directeur' => 'Directeur',
            'secretaire' => 'Secrétaire',
            'surveillant' => 'Surveillant'
        ];
    }

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => $id ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
            'role' => 'required|in:administrateur,directeur,secretaire,surveillant',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'actif' => 'boolean'
        ];
    }
}
