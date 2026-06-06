<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'image',
        'relationship',
        'mobile',
        'date_of_birth',
        'anniversary_date',
        'gotra',
        'occupation',
        'education',
        'blood_group',
        'hobbies',
        'native_place',
        'notes',
        'matrimony', // New field for matrimony status
        'gender',    // New field for gender
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'anniversary_date' => 'date',
        'matrimony' => 'boolean', // Cast matrimony to boolean
    ];

    /**
     * Get the customer that owns this family member.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}