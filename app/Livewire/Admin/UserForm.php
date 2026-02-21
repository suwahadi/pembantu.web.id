<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\{User, Role};
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

final class UserForm extends Component
{
    public ?int $user = null;
    public ?int $userId = null;

    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public string $status = 'active';
    public array $roles = [];
    public string $password = '';
    public string $password_confirmation = '';

    public array $roleOptions = [];
    public ?array $notification = null;

    protected $listeners = ['roleUpdated' => '$refresh'];

    public function mount(?int $user = null): void
    {
        $this->user = $user;
        $this->userId = $user;
        $this->roleOptions = Role::orderBy('label')->get()->map(function (Role $role) {
            return [
                'id' => $role->id,
                'label' => $role->label ?? ucfirst($role->name),
            ];
        })->toArray();

        if ($this->userId) {
            $model = User::with('roles')->find($this->userId);
            if ($model) {
                $this->name = $model->name;
                $this->email = $model->email;
                $this->phone = $model->phone;
                $this->status = $model->status ?? 'active';
                $this->roles = $model->roles->pluck('id')->map(fn ($id) => (int) $id)->toArray();
            }
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required', 'email', 'max:160',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'phone' => ['nullable', 'string', 'max:40'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')],
            'password' => [$this->userId ? 'nullable' : 'required', 'string', 'min:8', 'same:password_confirmation'],
            'password_confirmation' => [$this->userId ? 'nullable' : 'required_with:password', 'string', 'min:8'],
        ];
    }

    public function updated($field): void
    {
        $this->validateOnly($field);
    }

    public function save(): void
    {
        $data = $this->validate();
        $payload = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
        ];

        if (!$this->userId || ($this->password !== '')) {
            $payload['password'] = Hash::make($this->password);
        }

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $user->update($payload);
        } else {
            $user = User::create($payload);
            $this->userId = $user->id;
        }

        $user->roles()->sync($this->roles);

        session()->flash('success', $this->userId ? 'User berhasil diperbarui.' : 'User berhasil dibuat.');
        $this->redirectRoute('admin.users.index');
    }

    public function render()
    {
        return view('livewire.admin.user-form', [
            'roleOptions' => $this->roleOptions,
            'isEdit' => (bool) $this->userId,
        ]);
    }
}
