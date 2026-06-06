<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Village;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'admin_id',
        'village_id',
        'area',
        'name',
        'image',
        'father_name',
        'gotra',
        'label_name',
        'district',
        'ms_firm_name',
        'dno',
        'street_road',
        'address2',
        'city',
        'state',
        'pincode',
        'mobile',
        'whatsapp',
        'email',
        'age',
        'gender',
        'business_type',
        'business_name',
        'product_service',
        'office_address',
        'date_of_birth',
        'anniversary_date',
        'education',
        'occupation',
        'blood_group',
        'hobbies',
        'native_place',
        'status',
        'password',
        'otp',
        'otp_expires_at',
        'is_password_set',
        'admin_customer_id',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'otp',
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'is_password_set' => 'boolean',
        'date_of_birth' => 'date',
        'anniversary_date' => 'date',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically assign admin_customer_id before creating a customer
        static::creating(function ($customer) {
            if (is_null($customer->admin_customer_id)) {
                // Get the next admin_customer_id for this admin
                $lastCustomer = static::where('admin_id', $customer->admin_id)
                    ->orderBy('admin_customer_id', 'desc')
                    ->first();
                
                $customer->admin_customer_id = $lastCustomer ? $lastCustomer->admin_customer_id + 1 : 1;
            }
        });
    }

    public function admin(){
        return $this->belongsTo(Admin::class);
    }

    public function village(){
        return $this->belongsTo(Village::class, 'village_id', 'id');
    }

    public function customerPlans(){
        return $this->hasMany(CustomerPlan::class);
    }
    
    public function activeCustomerPlan(){
        return $this->hasOne(CustomerPlan::class)->where('status', 'active');
    }

    public function familyMembers(){
        return $this->hasMany(FamilyMember::class);
    }

    /**
     * Get all RSVPs made by this customer
     */
    public function eventRsvps()
    {
        return $this->hasMany(EventRSVP::class);
    }
    
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }
    

}