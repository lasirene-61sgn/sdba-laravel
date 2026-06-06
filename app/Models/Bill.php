<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'customer_id',
        'bill_number',
        'billing_type',
        'amount',
        'billing_period_start',
        'billing_period_end',
        'due_date',
        'status',
        'notes',
        'pdf_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Get the admin that owns this bill.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the customer associated with this bill.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class)->with('activeCustomerPlan');
    }
}