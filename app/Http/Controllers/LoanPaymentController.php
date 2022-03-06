<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoanResource;
use App\Models\Loan;
use App\Models\LoanPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoanPaymentController extends Controller
{
    public function weeklyPayment(Loan $loan, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric'], // Repay amount
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error'=> true,
                'message' => 'Validation error',
                'data' => $validator->errors()
            ], 422);
        }

        if ($loan->approval) {
            
            self::paymentCalculation($loan, $request);
    
            return response()->json([
                'success'=> true,
                'message' => 'Loan repaid successfully',
                'data' => new LoanResource($loan)
            ], 200);
        } else {
            return response()->json([
                'error'=> true,
                'message' => 'Loan not approved yet',
                'data' => new LoanResource($loan)
            ], 422);
        }        
    }

    private static function paymentCalculation(Loan $loan,  Request $request)
    {
        $loanPayment = $loan->loanPayment;

        if ($loanPayment->payment_amount_remaining > $request->amount) {
            // If the amount is less than the remaining amount, then update the remaining amount
            $loanPayment->payment_amount_remaining -= $request->amount;
            $loanPayment->payment_amount_paid += $request->amount;
        } else {
            // If the amount is greater than the remaining amount, the debt is paid off
            $loanPayment->weekly_payment_amount = 0;
            $loanPayment->payment_amount_remaining = 0;
            $loanPayment->payment_amount_paid = $loanPayment->weekly_payment_amount;
        }

        $loanPayment->weekly_payment_date = Carbon::parse($loanPayment->weekly_payment_date)->addWeeks(1);

        $loanPayment->save();
    }
}
