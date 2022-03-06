<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'amount', 'term', 'approval'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loanPayment()
    {
        return $this->hasOne(LoanPayment::class);
    }
}
