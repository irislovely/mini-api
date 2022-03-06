<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'total_weeks', 'weekly_payment_date', 'weekly_payment_amount', 'payment_amount_remaining', 'payment_amount_paid'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
