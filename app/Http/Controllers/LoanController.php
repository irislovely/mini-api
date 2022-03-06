<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoanResource;
use App\Models\Loan;
use App\Models\LoanPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric'], // Loan amount
            'term' => ['required', 'integer']   // Loan term, in months
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error'=> true,
                'message' => 'Validation error',
                'data' => $validator->errors()
            ], 422);
        }

        if (Auth::user()) {
            $loan = new Loan;

            $loan->amount = $request->amount;
            $loan->term = $request->term;
            $loan->approval = 0;
            
            $loan->user()->associate(Auth::user());
            $loan->save();

            $today = Carbon::now();
            $endDate = Carbon::now()->addMonths($request->term);
            $differenceInWeeks = floor($today->diff($endDate)->days / 7);

            $weekly_payment_date = $today->addWeeks(1);
            $weekly_payment_amount = round($request->amount / $differenceInWeeks, 4);
            $payment_amount_remaining = $request->amount;
            $payment_amount_paid = 0;

            $loanPayment = new LoanPayment;

            $loanPayment->total_weeks = $differenceInWeeks;
            $loanPayment->weekly_payment_date = $weekly_payment_date;
            $loanPayment->weekly_payment_amount = $weekly_payment_amount;
            $loanPayment->payment_amount_remaining = $payment_amount_remaining;
            $loanPayment->payment_amount_paid = $payment_amount_paid;

            $loanPayment->loan()->associate($loan);
            $loanPayment->save();            

            return response()->json([
                'success'=> true,
                'message' => 'Loan created successfully',
                'data' => new LoanResource($loan)
            ], 201);
        } else {
            return response()->json([
                'error'=> true,
                'message' => 'Validation error',
                'data' => ['error' => 'User is not verified']
            ], 422);
        }
    }

    public function adminApproval(Loan $loan, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'approval' => ['required', 'boolean']   // Loan approval, true or false
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error'=> true,
                'message' => 'Validation error',
                'data' => $validator->errors()
            ], 422);
        }

        if (Auth::user()->email == 'admin@test.com') { // Imagine admin@test.com is the admin email, created by seeder
            $loan->approval = $request->approval;
            $loan->save();

            return response()->json([
                'success'=> true,
                'message' => 'Loan approval updated successfully',
                'data' => new LoanResource($loan)
            ], 201);
        } else {
            return response()->json([
                'error'=> true,
                'message' => 'Validation error',
                'data' => ['error' => 'User is not admin']
            ], 422);
        }
    }

    public function show(Loan $loan)
    {
        return response()->json([
            'success'=> true,
            'message' => 'Loan retrieved successfully',
            'data' => new LoanResource($loan)
        ], 200);
    }
    
}
