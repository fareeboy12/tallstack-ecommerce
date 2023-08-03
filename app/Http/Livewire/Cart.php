<?php

namespace App\Http\Livewire;
use App\Models\Carts;
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
        $this->cart_items = Carts::orderBy('created_At', 'DESC')->get();
        $this->totalSum = $this->cart_items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
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
        $cart_items = Carts::where('id', $id)->first();
        if(!$cart_items){
            return;
        }
        
        if($cart_items->delete()){
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
