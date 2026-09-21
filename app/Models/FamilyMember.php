<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'social_link',
        'pdf',
        'bio_link',
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


    protected function biolink(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? 'https://jainwed.com/biodata/' . ltrim($value, '/') : null,
        );
    }
}
