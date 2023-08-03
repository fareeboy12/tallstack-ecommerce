<?php

namespace App\Http\Livewire;
use App\Models\Products;
use Livewire\WithPagination;
use Livewire\Component;

class ProductsListing extends Component
{
    use WithPagination;

    public function render()
    {
        $products = Products::paginate(12);
        return view('livewire.products-listing', compact('products'));
    }
}
