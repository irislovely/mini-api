<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'weekly_payment_date' => $this->weekly_payment_date,
            'weekly_payment_amount' => $this->weekly_payment_amount,            
            'payment_amount_paid' => $this->payment_amount_paid,
            'payment_amount_remaining' => $this->payment_amount_remaining,
        ];
    }
}
