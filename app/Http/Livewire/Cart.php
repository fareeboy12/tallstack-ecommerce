<?php

namespace App\Http\Livewire;
use App\Models\Carts;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Cart extends Component
{
    public $cart_items;
    public float $totalSum = 0.00;
    public string $selectedShippingMethod = "standard";
    public float $shippingCost = 0.00;
    public float $totalCost = 0.00;

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
    
        $this->emit('quantityUpdated');
    
        if (Auth::check()) {
            $user = Auth::user();
            // Insert user's data into the carts table
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

        
    }

    public function removeItem($id)
    {
        $cartItem = Carts::findOrFail($id);
        
        if (Auth::check() && $cartItem->user_id === Auth::user()->id) {
            $cartItem->delete();
            $this->emit("itemDeleted");
        }
        
        $this->getCartItems();
    }

    public function updateTotalCost()
    {
        $productsTotal = $this->cart_items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $this->totalCost = $productsTotal + $this->shippingCost;
    }
}
