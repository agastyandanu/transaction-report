<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    protected $fillable = ['merchant_id', 'outlet_id', 'transaction_time', 'staff', 'pay_amount', 'payment_type', 'customer_name', 'tax', 'change_amount', 'total_amount', 'payment_status'];
}
