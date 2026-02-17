<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class Register extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = 'visitor'; // visitor, agency
    public $terms = false;

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|min:10|max:13',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:visitor,agency',
            'terms' => 'accepted',
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.min' => 'Nama minimal 3 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'phone.required' => 'Nomor telepon wajib diisi',
            'phone.min' => 'Nomor telepon minimal 10 digit',
            'phone.max' => 'Nomor telepon maksimal 13 digit',
            'password.required' => 'Kata sandi wajib diisi',
            'password.min' => 'Kata sandi minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok',
            'terms' => 'Anda harus menerima syarat & ketentuan',
        ]);

        // Create user
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
        ]);

        // Assign role
        $role = Role::where('name', $this->role)->first();
        if ($role) {
            $user->roles()->attach($role);
        }

        // Auto-login
        auth()->login($user);

        if ($user->hasRole('agency')) {
            return redirect()->route('agency.dashboard');
        }

        return redirect()->route('orders.list');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.app');
    }
}
