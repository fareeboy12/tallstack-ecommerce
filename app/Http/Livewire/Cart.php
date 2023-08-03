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
    public string $selectedShippingMethod = "standard";
    public float $shippingCost = 0.00;
    public float $totalCost = 0.00;
    public string $couponCode = "";
    public float $couponPrice = 0.00;

    public function mount()
    {
        $this->getCartItems();
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
                return $item->product->price * $item->quantity;
            });

            if ($this->cart_items->isNotEmpty()) {
                if($this->cart_items->first()->shipping_method){
                    $this->selectedShippingMethod = $this->cart_items->first()->shipping_method;
                }
            }
            else{
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
    
        if ($action === 'increment') {
            $cartItem->update([
                'quantity' => $cartItem->quantity + 1,
            ]);
        } elseif ($action === 'decrement' && $cartItem->quantity > 1) {
            $cartItem->update([
                'quantity' => $cartItem->quantity - 1,
            ]);
        }
    
        $this->emit('success');
        session()->flash('successMessage', 'Quantity Updated.');
    
        if (Auth::check()) {
            $user = Auth::user();
            
            Carts::updateOrCreate(
                ['user_id' => $user->id, 'product_id' => $cartItem->product_id],
                ['quantity' => $cartItem->quantity]
            );
        }
    
        $this->getCartItems();
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
                // Calculate the old shipping cost from the first cart item (assuming they all have the same shipping method)
                $oldShippingCost = $carts->first()->shipping_fee;
                
                foreach ($carts as $cart) {
                    $cart->shipping_method = $this->selectedShippingMethod;
                    $cart->shipping_fee = $this->shippingCost;
                    $cart->total = $cart->total - $oldShippingCost + $this->shippingCost;
                    $cart->save();
                }
            }

            // Debugging statements
            \Log::info("Selected Shipping Method: " . $this->selectedShippingMethod);
            \Log::info("New Shipping Cost: " . $this->shippingCost);
            \Log::info("Old Shipping Cost: " . $oldShippingCost);

    
            // $this->emit('shippingUpdated');
            $this->emit('success');
            session()->flash('successMessage', 'Shipping Method Updated.');
    
            $this->getCartItems();
        }
    }
    

    public function removeItem($id)
    {
        $cartItem = Carts::findOrFail($id);
        
        if (Auth::check() && $cartItem->user_id === Auth::user()->id) {
            $cartItem->delete();
            // $this->emit("itemDeleted");
            $this->emit('success');
            session()->flash('successMessage', 'Product removed from the Cart.');
        }
        
        $this->getCartItems();
    }

    public function updateTotalCost()
    {
        $productsTotal = $this->cart_items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
    
        $this->totalCost = $productsTotal + $this->shippingCost - $this->couponPrice;
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
                    $cart->total = ($cart->product->price * $cart->quantity) + $cart->shipping_fee - $this->couponPrice;
                    $cart->save();
                }
    
                // Update the total cost for the component
                $this->updateTotalCost();
    
                // Clear the coupon code field
                $this->couponCode = '';
                $this->couponPrice = 0.00;
    
                // Optionally, you can emit an event or display a success message
                // $this->emit('couponApplied');

                $this->emit('success');
                session()->flash('successMessage', 'Coupon Applied.');

            } else {
                // Coupon not found, handle accordingly (emit an event or display an error message)
                // $this->emit('couponNotApplied');
                $this->emit('error');
                session()->flash('errorMessage', 'Wrong Coupon code provided.');
            }

            $this->getCartItems();
        }
    }

    public function removeCoupon()
    {
        $user = Auth::user();
        $carts = Carts::where('user_id', $user->id)->get();

        foreach ($carts as $cart) {
            $cart->coupon_code = '';
            $cart->coupon_price = 0.00;
            $cart->total = ($cart->product->price * $cart->quantity) + $cart->shipping_fee;
            $cart->save();
        }

        // Update the total cost for the component
        $this->updateTotalCost();

        $this->couponCode = '';
        $this->couponPrice = 0.00;

        $this->emit('success');
        session()->flash('successMessage', 'Coupon Removed.');
    }
}
