<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Models\Deposit;

class DepositService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
    }
    public function approveDeposit($depositId)
    {
        
        $deposit = Deposit::find($depositId);

        $deposit->update(['status' =>  TransactionStatus::APPROVED]);

        $deposit->user->increment('total_deposited', $deposit->amount);
        $deposit->user->increment('main_balance', $deposit->amount);
       
    }
}
