<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    public function submit()
    {
        $this->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8',
        ], [
            'email.required' => 'Alamat email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Akun dengan email ini tidak ditemukan',
            'password.required' => 'Kata sandi wajib diisi',
            'password.min' => 'Kata sandi minimal 8 karakter',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $user = Auth::user();
            
            session()->flash('success', 'Selamat datang kembali, ' . $user->name . '!');

            if ($user->hasRole('admin')) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->hasRole('agency')) {
                return redirect()->intended(route('agency.dashboard'));
            }
            
            return redirect()->intended(route('orders.list'));
        }

        $this->addError('password', 'Kata sandi yang Anda masukkan salah');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.app');
    }
}
