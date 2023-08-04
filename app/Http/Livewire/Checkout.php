<?php

namespace App\Http\Livewire;
use App\Models\Products; 
use App\Models\User;
use App\Models\Carts;
use App\Models\Orders;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Checkout extends Component
{
    public $cart_items;
    public float $totalSum = 0.00;
    public float $grandTotal = 0.00;

    public $user_id;
    public $order_number;
    public $first_name;
    public $last_name;
    public $company;
    public $country;
    public $address1;
    public $address2;
    public $city;
    public $state;
    public $postcode;
    public $phone;
    public $email;
    public $notes;
    public $subtotal;
    public $shipping_fee;
    public $coupon_price;
    public $total;
    public $payment_method;
    public $payment_status;
    public $order_status;

    public function mount()
    {
        $this->getCartData();
        $this->updateTotalCost();
    }

    public function getCartData()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->cart_items = Carts::where('user_id', $user->id)
                ->orderBy('created_at', 'DESC')
                ->get();
    
            $this->totalSum = $this->cart_items->sum(function ($item) {
                return isset($item->product->sale_price)
                    ? $item->product->sale_price * $item->quantity
                    : $item->product->price * $item->quantity;
            });

            $this->shipping_fee = $this->cart_items->first()->shipping_fee;
    
            if ($this->cart_items->isNotEmpty()) {
                if ($this->cart_items->first()->shipping_method) {
                    $this->selectedShippingMethod = $this->cart_items->first()->shipping_method;
                }
            } else {
                $this->selectedShippingMethod = "standard";
            }
    
        } else {
            $this->cart_items = collect(); // Empty collection for guests
            $this->totalSum = 0.00;
        }
    }

    public function render()
    {
        return view('livewire.checkout');
    }

    public function confirmOrder()
    {
        // Generate random and unique order number
        $orderNumber = Str::random(8);

        // Store order details in orders table
        $order = Orders::create([
            'user_id' => Auth::id(),
            'order_number' => $orderNumber,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company' => $this->company,
            'country' => $this->country,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'city' => $this->city,
            'state' => $this->state,
            'postcode' => $this->postcode,
            'phone' => $this->phone,
            'email' => $this->email,
            'notes' => $this->notes,
            'subtotal' => $this->totalSum,
            'shipping_fee' => $this->shipping_fee,
            'coupon_fee' => $this->coupon_price,
            'total' => $this->grandTotal,
            'payment_method' => 'cod',
            'payment_status' => '',
            'order_status' => 'processing',
        ]);

        // Store order items in orderitems table
        foreach ($this->cart_items as $cartItem) {
            OrderItems::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
            ]);
        }

        // Clear cart items
        $this->cart_items = collect();
        $this->emit('success');
        session()->flash('successMessage', $orderNumber);

        // Redirect to success page
        // return redirect()->route('checkout.success');
    }

    public function updateTotalCost()
    {
        $productsTotal = $this->cart_items->sum(function ($item) {
            return $item->total;
        });
    
        // Get the coupon price from the first record
        $firstCartItem = $this->cart_items->first();
        $couponPrice = $firstCartItem->coupon_price;
    
        // Get the shipping fee from the first record
        $shippingFee = $firstCartItem->shipping_fee;
    
        $this->grandTotal = $productsTotal + $shippingFee - $couponPrice;
    }
}
