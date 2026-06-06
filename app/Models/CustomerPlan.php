<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerPlan extends Model
{
    use HasFactory;

    protected $fillable =[
        'admin_id',
        'customer_id',
        'plan_type',
        'start_date',
        'next_due_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function admin(){
        return $this->belongsTo(Admin::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
