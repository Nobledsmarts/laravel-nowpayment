<!--
  Heads up! 👋

  Plugins:
    - @tailwindcss/forms
-->

<div class="mx-auto max-w-screen-xl px-4 py-16 sm:px-6 lg:px-8">
    @if(!$this->depositInitiated)
        <div class="mx-auto max-w-lg">
            <h1 class="text-center text-2xl font-bold text-indigo-600 sm:text-3xl">
                Make Deposit
            </h1>
    
            <p class="mx-auto mt-4 max-w-md text-center text-gray-500">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Obcaecati sunt dolores deleniti
                inventore quaerat mollitia?
            </p>
        
            @session('error')
            <div class="my-2 bg-red-600 text-white p-3">

                {{ $value }}
            </div>
            @endsession

            <form wire:submit='initDeposit' class="mb-0 mt-6 space-y-4 rounded-lg p-4 shadow-lg sm:p-6 lg:p-8">
                <p class="text-center text-lg font-medium">Fill in the details below</p>
        
                <div>
                    <label for="amount" class="sr-only">Amount</label>
            
                    <div class="relative">
                        <input
                        wire:model='amount'
                        type="amount"
                        class="w-full rounded-lg border-gray-200 p-4 pe-12 text-sm shadow-sm"
                        placeholder="Enter amount"
                        />
                        @error('amount')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
        
                <div>
                    <label for="amount" class="sr-only">Wallet</label>
            
                    <div class="relative">
                        <select wire:model='selectedCurrency' class="w-full rounded-lg border-gray-200 p-4 pe-12 text-sm shadow-sm" name="" id="">
                            <option selected class="text-white bg-dark">
                                Select Wallet
                            </option>
                            @foreach ($this->currencies as $currency)
                            <option value="{{ $currency }}">
                                {{ $currency }} 
                            </option>
                            @endforeach
                        </select>
                        @error('wallet')
                        {{ $message }}
                    @enderror
                    </div>
                </div>
        
                <button
                
                type="submit"
                class="block w-full rounded-lg bg-indigo-600 px-5 py-3 text-sm font-medium text-white"
                >
                <span wire:loading.class='d-flex' wire:target='initDeposit'>
                    Loading
                </span>
                <span wire:loading.remove wire:target='initDeposit'>
                    Submit
                </span>
                </button>
            </form>
        </div>
    @else
    <div class="mx-auto max-w-lg">
        <div class="flex-wrap md:flex">
            <div class="mx-auto">
              {{-- <img class="mx-auto mt-12 h-52 w-52 rounded-lg border p-2 md:mt-0" src="https://i.imgur.com/FQS7fFC.png" alt="step" /> --}}
              <div class="mb-4">
                <h1 class="font-laonoto mt-4 text-center text-xl font-bold">Payment Preview</h1>
                <p class="mt-2 text-center font-semibold text-gray-600">PLEASE SEND EXACTLY </p>
                <p class="mt-1 text-center font-medium ">
                    <span class="text-green-500"> {{ $this->deposit->pay_amount }} </span>
                    {{ strtoupper($this->deposit->network) }}  {{ strtoupper($this->deposit->currency) }}
                    <br>
                        TO 
                    <span class="text-success text-xs">
                        {{ $this->deposit->destination_wallet_address }}
                    </span>
                </p>
              </div>
                <div class="mx-auto flex justify-center text-center py-4 rounded-lg border p-2 md:mt-0">
                    {!!
                    QrCode::size(208)
                    ->margin(2)
                    ->generate($this->deposit->destination_wallet_address);
                    !!}
                </div>
             
              
                <a href="{{ route('home') }}">
                    <button class="mx-auto mt-4 block rounded-md border bg-blue-500 px-6 py-2 text-white outline-none">
                      Done
                    </button>
                </a>
            </div>
         
          </div>
    </div>
    @endif
  </div>