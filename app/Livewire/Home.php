<?php

namespace App\Livewire;

use App\Models\Deposit;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Home extends Component
{
    #[Validate(['required', 'numeric'], message: [
        'amount.required' => 'Please enter amount',
        'amount.numeric' => 'Invalid amount',
    ])]
    public $amount;

    public $transactionId = null;

    #[Validate(['required'], attribute:'currency')]
    public $selectedCurrency;

    #[Computed]
    public function currencies(){
        if(Cache::has('currencies') && !count(cache('currencies'))) {
            Cache::forget('currencies');
        }

        return Cache::remember('currencies', now()->addMinutes(30), function () {
            try {
                return Http::nowpayment()
                    ->get('/merchant/coins')
                    ->json('selectedCurrencies');
            } catch(\Exception){
                return [];
            }
        });
    }

    #[Computed]
    public function deposit(){
        return Deposit::where('transaction_id', $this->transactionId)->first();
    }
      
    #[Computed]
    public function depositInitiated(): bool {
        return $this->deposit ? true : false; 
    }

    public function mount($transactionId = null)
    {

        if ($transactionId) {
            $this->transactionId = $transactionId;  
            unset($this->deposit);
            unset($this->depositInitiated);
        }
    }

    public function initDeposit()
    {

        $this->validate();

        return $this->handleNowPaymentDeposit();
    }

    #[Computed]
    public function user() {
        // return User::with(['deposits'])->find(auth()->id());

        return User::first();
    }

    public function createDeposit($details){

        $deposit = $this->user->deposits()->create([
            'transaction_id' => $details['transaction_id'],
            'amount' => $this->amount,
            'currency' => $details['pay_currency'],
            'network' => $details['network'],
            'pay_amount' => $details['pay_amount'],
            'destination_wallet_address' => $details['pay_address'],
        ]);

        return $deposit;
    }


    public function handleNowPaymentDeposit(){

        $transactionId = str_replace('-', '', Str::orderedUuid());

        try {
            $response = Http::nowpayment()->post('/payment', [
                'price_amount' => $this->amount,
                'price_currency' => 'usd',
                "pay_currency" => strtolower($this->selectedCurrency),
                "order_id" => $transactionId,
                "ipn_callback_url" => route('deposit-callback'),
                "order_description" =>  'Account Top Up by ' . $this->user->name,
            ]);
        } catch (Exception $e) {
            return Session::flash('error', 'Unable to initialize deposit. please try again');
        }

        $details = $response->json();

        $deposit = $this->createDeposit([
            'transaction_id' => $transactionId, 
            ...$details
        ]);

        return to_route('home', ['transactionId' => $deposit->transaction_id]);
    }

    public function render()
    {
        return view('livewire.home');
    }
}
