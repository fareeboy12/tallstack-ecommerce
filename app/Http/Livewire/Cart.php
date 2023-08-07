<?php

namespace App\Http\Livewire;
use App\Models\Carts;
use App\Models\User;
use App\Models\Coupons;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Cart extends Component
{
    public $cart_items;
    public float $totalSum = 0.00;
    public float $grandTotal = 0.00;
    public string $selectedShippingMethod = "standard";
    public float $shippingCost = 0.00;
    public float $totalCost = 0.00;
    public string $couponCode = "";
    public float $couponPrice = 0.00;

    public function mount()
    {
        $this->getCartItems();
        $this->updateTotalCost();
    }

    public function render()
    {
        return view('livewire.cart');
    }

    public function getCartItems()
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
    
    public function updateQuantity($id, $action)
    {
        $cartItem = Carts::findOrFail($id);
    
        $quantity = $cartItem->quantity;
    
        if ($action === 'increment') {
            $quantity++;
        } elseif ($action === 'decrement' && $quantity > 1) {
            $quantity--;
        }
    
        if($cartItem->product->sale_price){
            $productPrice = $cartItem->product->sale_price;
        }
        else{
            $productPrice = $cartItem->product->price;
        }
    
        $cartItem->update([
            'quantity' => $quantity,
            'total' => $productPrice * $quantity
        ]);
    
        if (Auth::check()) {
            $user = Auth::user();
            
            Carts::updateOrCreate(
                ['user_id' => $user->id, 'product_id' => $cartItem->product_id],
                ['quantity' => $quantity]
            );
        }
    
        $this->getCartItems();
        $this->updateTotalCost();
        $this->emit('success');
        session()->flash('successMessage', 'Quantity Updated.');
    }
    
    public function updateShippingCost()
    {
        if ($this->selectedShippingMethod === 'standard') {
            $this->shippingCost = 0;
        } elseif ($this->selectedShippingMethod === 'fast') {
            $this->shippingCost = 20.00;
        } elseif ($this->selectedShippingMethod === 'urgent') {
            $this->shippingCost = 40.00;
        }

        if (Auth::check()) {
            $user = Auth::user();
            $carts = Carts::where('user_id', $user->id)->get();
            $oldShippingCost = 0.00;
    
            if ($carts) {
                $oldShippingCost = $carts->first()->shipping_fee;
                
                foreach ($carts as $cart) {
                    $cart->shipping_method = $this->selectedShippingMethod;
                    $cart->shipping_fee = $this->shippingCost;
                    $cart->save();
                }
            }

            $this->emit('success');
            session()->flash('successMessage', 'Shipping Method Updated.');
    
            $this->getCartItems();
            $this->updateTotalCost();
        }
    }
    
    public function removeItem($id)
    {
        $cartItem = Carts::findOrFail($id);
        
        if (Auth::check() && $cartItem->user_id === Auth::user()->id) {
            $cartItem->delete();

            $this->emit('success');
            session()->flash('successMessage', 'Product removed from the Cart.');
        }
        
        $this->getCartItems();
    }

    public function updateTotalCost()
    {
        $productsTotal = $this->cart_items->sum(function ($item) {
            return $item->total;
        });
    
        // Get the coupon price from the first record
        $firstCartItem = $this->cart_items->first();
        if(isset($firstCartItem->coupon_price)){
            $couponPrice = $firstCartItem->coupon_price;
        }
        else{
            $couponPrice = 0.00;
        }
    
        // Get the shipping fee from the first record
        if(isset($firstCartItem->shipping_fee)){
            $shippingFee = $firstCartItem->shipping_fee;
        }
        else{
            $shippingFee = 0.00;
        }
    
        $this->grandTotal = $productsTotal + $shippingFee - $couponPrice;
    }

    public function applyCoupon()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $coupon = Coupons::where('coupon_code', $this->couponCode)->first();
    
            if ($coupon) {
                $this->couponPrice = $coupon->coupon_price;
    
                // Update each cart item with the coupon information
                $carts = Carts::where('user_id', $user->id)->get();
    
                foreach ($carts as $cart) {
                    $cart->coupon_code = $this->couponCode;
                    $cart->coupon_price = $this->couponPrice;
                    $cart->save();
                }
    
                
                $this->couponCode = '';
                $this->couponPrice = 0.00;

                $this->emit('success');
                session()->flash('successMessage', 'Coupon Applied.');

            } else {
                $this->emit('error');
                session()->flash('errorMessage', 'Wrong Coupon code provided.');
            }

            $this->getCartItems();
            $this->updateTotalCost();
        }
    }

    public function removeCoupon()
    {
        $user = Auth::user();
        $carts = Carts::where('user_id', $user->id)->get();

        foreach ($carts as $cart) {
            $cart->coupon_code = '';
            $cart->coupon_price = 0.00;
            $cart->save();
        }

        $this->getCartItems();
        $this->updateTotalCost();

        $this->couponCode = '';
        $this->couponPrice = 0.00;

        $this->emit('success');
        session()->flash('successMessage', 'Coupon Removed.');
    }
}
