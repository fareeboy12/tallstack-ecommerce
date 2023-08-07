@section('title')
    Checkout
@endsection
<div class="container p-12 mx-auto">
    <div class="mx-auto">
        <div id="alert-border-3" class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert" 
            x-data="{ showSuccessMessage: false }"
            x-show="showSuccessMessage"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            x-init="@this.on('success', () => { showSuccessMessage = true; setTimeout(() => showSuccessMessage = false, 5000)})">
            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <div class="ml-3 text-sm font-medium">
              Thank you for ordering. Your Order is confirmed. Your order number is: {{ session('successMessage') }}.
            </div>
            <button type="button" @click="showSuccessMessage = false" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700"  data-dismiss-target="#alert-border-3" aria-label="Close">
                <span class="sr-only">Dismiss</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    </div>
    <div class="flex flex-col w-full px-0 mx-auto md:flex-row">
        <div class="flex flex-col md:w-full">
            <h2 class="mb-4 font-bold md:text-xl text-heading ">Shipping Address</h2>
            <div class="">
                <div class="space-x-0 lg:flex lg:space-x-4">
                    <div class="w-full lg:w-1/2">
                        <label for="first_name" class="block mb-3 text-sm font-semibold text-gray-500">First Name <span class="text-red-500">*</span></label>
                        <input wire:model="first_name" id="first_name" name="first_name" type="text" placeholder="First Name" class="w-full px-4 py-3 text-sm border border-gray-300 rounded lg:text-sm focus:outline-none focus:ring-1 focus:ring-blue-600" required>
                    </div>
                    <div class="w-full lg:w-1/2 ">
                        <label for="last_name" class="block mb-3 text-sm font-semibold text-gray-500">Last Name<span class="text-red-500">*</span></label>
                        <input wire:model="last_name" id="last_name" name="last_name" type="text" placeholder="Last Name" class="w-full px-4 py-3 text-sm border border-gray-300 rounded lg:text-sm focus:outline-none focus:ring-1 focus:ring-blue-600" required>
                    </div>
                </div>
                <div class="space-x-0 lg:flex lg:space-x-4 mt-4">
                    <div class="w-full lg:w-1/2">
                        <label for="email" class="block mb-3 text-sm font-semibold text-gray-500">Email <span class="text-red-500">*</span></label>
                        <input wire:model="email" id="email" name="email" type="text" placeholder="Email" class="w-full px-4 py-3 text-sm border border-gray-300 rounded lg:text-sm focus:outline-none focus:ring-1 focus:ring-blue-600" required>
                    </div>

                    <div class="w-full lg:w-1/2">
                        <label for="phone" class="block mb-3 text-sm font-semibold text-gray-500">Phone <span class="text-red-500">*</span></label>
                        <input wire:model="phone" id="phone" name="phone" type="text" placeholder="Phone" class="w-full px-4 py-3 text-sm border border-gray-300 rounded lg:text-sm focus:outline-none focus:ring-1 focus:ring-blue-600" required>
                    </div>
                </div>
                <div class="space-x-0 lg:flex lg:space-x-4 mt-4">
                    <div class="w-full lg:w-1/2">
                        <label for="address1" class="block mb-3 text-sm font-semibold text-gray-500">Address 1 <span class="text-red-500">*</span></label>
                        <input wire:model="address1" id="address1" name="address1" type="text" placeholder="Address 1" class="w-full px-4 py-3 text-sm border border-gray-300 rounded lg:text-sm focus:outline-none focus:ring-1 focus:ring-blue-600" required>
                    </div>

                    <div class="w-full lg:w-1/2">
                        <label for="address2" class="block mb-3 text-sm font-semibold text-gray-500">Address 2 <span class="text-red-500">*</span></label>
                        <input wire:model="address2" id="address2" name="address2" type="text" placeholder="Address 2" class="w-full px-4 py-3 text-sm border border-gray-300 rounded lg:text-sm focus:outline-none focus:ring-1 focus:ring-blue-600" required>
                    </div>
                </div>
                <div class="space-x-0 lg:flex lg:space-x-4 mt-4">
                    <div class="w-full lg:w-1/2">
                        <label for="city" class="block mb-3 text-sm font-semibold text-gray-500">City <span class="text-red-500">*</span></label>
                        <input wire:model="city" id="city" name="city" type="text" placeholder="City" class="w-full px-4 py-3 text-sm border border-gray-300 rounded lg:text-sm focus:outline-none focus:ring-1 focus:ring-blue-600" required>
                    </div>
                    <div class="w-full lg:w-1/2 ">
                        <label for="state" class="block mb-3 text-sm font-semibold text-gray-500">State <span class="text-red-500">*</span></label>
                        <input wire:model="state" id="state" name="state" type="text" placeholder="State" class="w-full px-4 py-3 text-sm border border-gray-300 rounded lg:text-sm focus:outline-none focus:ring-1 focus:ring-blue-600" required>
                    </div>
                </div>
                <div class="space-x-0 lg:flex lg:space-x-4 mt-4">
                    <div class="w-full lg:w-1/2">
                        <label for="postcode" class="block mb-3 text-sm font-semibold text-gray-500">Postal Code <span class="text-red-500">*</span></label>
                        <input wire:model="postcode" id="postcode" name="postcode" type="text" placeholder="Postal Code" class="w-full px-4 py-3 text-sm border border-gray-300 rounded lg:text-sm focus:outline-none focus:ring-1 focus:ring-blue-600" required>
                    </div>
                    <div class="w-full lg:w-1/2 ">
                        <label for="country" class="block mb-3 text-sm font-semibold text-gray-500">Country <span class="text-red-500">*</span></label>
                        <input wire:model="country" id="country" name="country" type="text" placeholder="Country" class="w-full px-4 py-3 text-sm border border-gray-300 rounded lg:text-sm focus:outline-none focus:ring-1 focus:ring-blue-600" required>
                    </div>
                </div>

                <div class="space-x-0 lg:flex lg:space-x-4 mt-4">
                    <div class="w-full">
                        <label for="company" class="block mb-3 text-sm font-semibold text-gray-500">Company (Optional)</label>
                        <input wire:model="company" id="company" name="company" type="text" placeholder="Company" class="w-full px-4 py-3 text-sm border border-gray-300 rounded lg:text-sm focus:outline-none focus:ring-1 focus:ring-blue-600">
                    </div>
                </div>
                <div class="relative pt-3 xl:pt-6">
                    <label for="notes" class="block mb-3 text-sm font-semibold text-gray-500"> Notes (Optional)</label>
                    <textarea wire:model="notes" id="notes" name="notes" class="flex items-center w-full px-4 py-3 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-600" rows="4" placeholder="Notes for delivery"></textarea>
                </div>
                <div class="mt-4">
                    <button wire:click="confirmOrder" class="w-full px-6 py-2 text-blue-200 bg-blue-600 hover:bg-blue-900">Process</button>
                </div>
            </div>
        </div>
        <div class="flex flex-col w-full ml-0 lg:ml-12 lg:w-2/5">
            <div class="pt-12 md:pt-0 2xl:ps-4">
                <h2 class="text-xl font-bold">Order Summary
                </h2>
                <div class="mt-8">
                    <div class="flex flex-col space-y-4">
                        @foreach ($cart_items as $item)
                        <div class="flex space-x-4">
                            <div>
                                <img src="{{ asset('storage/' . $item->product->thumbnail) }}" alt="{{ $item->product->title }}" class="w-20 h-20">
                            </div>
                            <div>
                                <h2 class="text-md font-bold">{{ $item->product->title }}</h2>
                                <p class="text-sm">Quantity: {{ $item->quantity }}</p>
                                <span class="text-red-600">Price</span> ${{ $item->product->sale_price ?? $item->product->price }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex p-4 mt-4">
                    <h2 class="text-xl font-bold">ITEMS {{ count($cart_items) }}</h2>
                </div>
                <div class="flex items-center w-full py-4 text-sm font-semibold border-b border-gray-300 lg:py-5 lg:px-3 text-heading last:border-b-0 last:text-base last:pb-0">
                    Subtotal:<span class="ml-2">${{ number_format($totalSum, 2) }}</span>
                </div>
                <div class="flex items-center w-full py-4 text-sm font-semibold border-b border-gray-300 lg:py-5 lg:px-3 text-heading last:border-b-0 last:text-base last:pb-0">
                    Shipping Charges:<span class="ml-2">${{ $shipping_fee ?? '0' }}</span>
                </div>
                @if(isset($item->coupon_fee))
                <div class="flex items-center w-full py-4 text-sm font-semibold border-b border-gray-300 lg:py-5 lg:px-3 text-heading last:border-b-0 last:text-base last:pb-0">
                    Promo Discout:<span class="ml-2">-${{ $item->coupon_fee }}</span>
                </div>
                @endif
                <div class="flex items-center w-full py-4 text-sm font-semibold border-b border-gray-300 lg:py-5 lg:px-3 text-heading last:border-b-0 last:text-base last:pb-0">
                    Delivery Method:<span class="ml-2">Cash on Delivery</span>
                </div>
                <div class="flex items-center w-full py-4 text-sm font-semibold border-b border-gray-300 lg:py-5 lg:px-3 text-heading last:border-b-0 last:text-base last:pb-0">
                    Total:<span class="ml-2">${{ number_format($grandTotal, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>