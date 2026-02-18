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
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|min:10|max:15|unique:users,phone',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:visitor,agency',
            'terms' => 'accepted',
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'name.min' => 'Nama minimal 3 karakter',
            'name.max' => 'Nama maksimal 50 karakter',
            'email.required' => 'Alamat email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email ini sudah terdaftar',
            'phone.required' => 'Nomor telepon wajib diisi',
            'phone.min' => 'Nomor telepon minimal 10 digit',
            'phone.max' => 'Nomor telepon maksimal 15 digit',
            'phone.unique' => 'Nomor telepon sudah terdaftar',
            'password.required' => 'Kata sandi wajib diisi',
            'password.min' => 'Kata sandi minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok',
            'terms.accepted' => 'Anda harus menyetujui syarat & ketentuan',
            'role.required' => 'Pilih jenis akun Anda',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () {
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
                $user->roles()->attach($role->id);
            } else {
                // Fallback to visitor if role not found
                $visitorRole = Role::where('name', 'visitor')->first();
                if ($visitorRole) {
                    $user->roles()->attach($visitorRole->id);
                }
            }

            // Auto-login
            auth()->login($user);
        });

        $user = auth()->user();
        session()->flash('success', 'Akun berhasil dibuat. Selamat datang di Pembantu.web.id!');

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
