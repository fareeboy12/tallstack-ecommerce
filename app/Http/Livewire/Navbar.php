<?php

namespace App\Http\Livewire;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Livewire\Component;

class Navbar extends Component
{
    public $user;
    public $loggedIn;
    public $isOpen = false;

    public function mount()
    {
        $this->getUser();
    }

    public function toggleMenu()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function render()
    {
        return view('livewire.navbar');
    }

    public function getUser()
    {
        $this->user = Auth::check() ? Auth::user() : null;
    }
}
