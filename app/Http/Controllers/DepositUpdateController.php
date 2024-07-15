<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Models\Deposit;
use App\Services\DepositService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepositUpdateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        try {

            $status = $request->input('payment_status');
            $transactionId = $request->input('order_id');

            $depositStatus = match ($status) {
                'waiting' => TransactionStatus::PENDING,
                'confirming', 'confirmed', 'sending' => TransactionStatus::PROCESSING,
                'failed', 'refunded', 'expired' => TransactionStatus::DECLINED,
                'finished' => TransactionStatus::APPROVED,
                default => TransactionStatus::PENDING,
            };

            $deposit = Deposit::where('transaction_id', $transactionId)->first();

            if (!$deposit) return;

            if ($depositStatus->value == TransactionStatus::APPROVED->value) {
                $depositService = new DepositService;

                $depositService->approveDeposit($deposit->id);

                Log::info("{$deposit->user->username} Deposit approved successfully");

                return ['deposit approved successfully'];
            } else {
                $deposit->update(['status' => $depositStatus->value]);
                Log::info("{$deposit->user->username} Deposit status set to {$deposit->status}");

                return ["{$deposit->user->username} Deposit status set to {$deposit->status}"];
            }

        } catch (\Exception $e) {
           
            Log::error('deposit-error', [$deposit, 'message' => $e->getMessage()]);
        }
    }
}
