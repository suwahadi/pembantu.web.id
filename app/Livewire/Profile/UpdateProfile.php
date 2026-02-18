<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdateProfile extends Component
{
    // Profile info
    public $name;
    public $email;
    public $phone;
    public $address;

    // Password change
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->address = $user->address;
    }

    public function updateContact()
    {
        $user = Auth::user();

        $this->validate([
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|min:10|max:15|unique:users,phone,' . $user->id,
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Alamat email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain',
            'phone.required' => 'Nomor telepon wajib diisi',
            'phone.unique' => 'Nomor telepon sudah digunakan oleh akun lain',
            'address.max' => 'Alamat maksimal 500 karakter',
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        session()->flash('contact_success', 'Profil kontak berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi',
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok',
            'new_password.required' => 'Kata sandi baru wajib diisi',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi kata sandi tidak cocok',
        ]);

        Auth::user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        session()->flash('password_success', 'Kata sandi berhasil diubah.');
    }

    public function render()
    {
        return view('livewire.profile.update-profile')
            ->layout('layouts.app', ['title' => 'Pengaturan Profil']);
    }
}
