<?php

namespace App\Http\Livewire;
use App\Models\Products;
use App\Models\Carts;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductDetail extends Component
{
    public $slug;
    public $cart;
    public $disabled = false;
    public int $product_id = 0;
    public string $quantity = '1';
    public int $price = 0;
    public int $sale_price = 0;

    public function mount($slug)
    {
        $this->slug = $slug;
        $product = Products::where('slug', $this->slug)->first();
        if ($product) {
            $this->product_id = $product->id;
            $this->price = $product->price;
            if($product->sale_price){
                $this->sale_price = $product->sale_price;
            }
        } else {
            abort(404);
        }
    }

    public function render()
    {
        $product = Products::where('slug', $this->slug)->first();
        
        if (!$product) {
            abort(404);
        }

        $disabled = true;

        if (Auth::check()) {
            $user = Auth::user();
            if(!$user){
                return view('livewire.product-detail', compact('product', 'disabled'));
            }
        }
        
        return view('livewire.product-detail', compact('product'));
    }

    public function addToCart()
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (intval($this->quantity) <= 0) {
                // session()->flash('errorMessage', 'Quantity must be greater than 0.');
                $this->emit("errorMessage");
                return;
            }
    
            $cartItem = new Carts();
            $cartItem->user_id = $user->id;
            $cartItem->product_id = $this->product_id;
            $cartItem->quantity = intval($this->quantity);
            $cartItem->price = $this->price;
            $cartItem->sale_price = $this->sale_price;
    
            if($cartItem->sale_price > 0){
                $cartItem->total = $this->sale_price * intval($this->quantity);
            }
            else{
                $cartItem->total = $this->price * intval($this->quantity);
            }
            
            if($cartItem->save()){
                // session()->flash('successMessage', 'Product added to cart.');
                $this->emit("successMessage");
            }
            else{
                // session()->flash('errorMessage', 'Error.');
                $this->emit("errorMessage");
            }
    
            $this->quantity = '1';
            $this->total = '';
        }
    }
}
