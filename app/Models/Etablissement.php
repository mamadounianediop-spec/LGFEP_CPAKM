<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etablissement extends Model
{
    protected $fillable = [
        'nom',
        'ninea',
        'adresse',
        'telephone',
        'email',
        'responsable',
        'description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'nom' => 'required|string|max:255',
            'ninea' => 'nullable|string|max:255|unique:etablissements,ninea,' . $id,
            'adresse' => 'required|string',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:etablissements,email,' . $id,
            'responsable' => 'required|string|max:255',
            'description' => 'nullable|string'
        ];
    }
}
