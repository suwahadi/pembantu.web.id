<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{User, Role};
use Illuminate\Support\Facades\Auth;

final class UserManager extends Component
{
    use WithPagination;

    private const STATUSES = [
        'active' => 'Aktif',
        'inactive' => 'Nonaktif',
    ];

    public string $search = '';
    public string $status = '';
    public string $role = '';

    public ?int $fixedRoleId = null;
    public ?string $fixedRoleName = null;
    public bool $lockRoleFilter = false;
    public ?string $redirectRoute = null;

    public bool $confirmingDelete = false;
    public ?int $deleteUserId = null;
    public ?array $alert = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'role'   => ['except' => ''],
    ];

    protected $listeners = ['userSaved' => '$refresh'];

    public function mount(?int $fixedRoleId = null, ?string $fixedRoleName = null, bool $lockRoleFilter = false, ?string $redirectRoute = null)
    {
        $this->fixedRoleId = $fixedRoleId;
        $this->fixedRoleName = $fixedRoleName;
        $this->lockRoleFilter = $lockRoleFilter;
        $this->redirectRoute = $redirectRoute;

        if (!$this->fixedRoleId && $this->fixedRoleName) {
            $role = Role::query()->where('name', $this->fixedRoleName)->first();
            $this->fixedRoleId = $role?->id;
        }

        if ($this->fixedRoleId) {
            $this->role = (string) $this->fixedRoleId;
        }

        if (session()->has('success')) {
            $this->alert = [
                'type' => 'success',
                'message' => session('success'),
                'timestamp' => now()->timestamp,
            ];
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingRole(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'status', 'role']);
        $this->resetPage();
    }

    public function toggleStatus(int $userId): void
    {
        if ($userId === Auth::id()) {
            $this->notify('error', 'Tidak dapat mengubah status akun sendiri.');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->notify('error', 'User tidak ditemukan.');
            return;
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        $this->notify('success', 'Status user berhasil diperbarui.');
    }

    public function confirmDelete(int $userId): void
    {
        if ($userId === Auth::id()) {
            $this->notify('error', 'Tidak dapat menghapus akun sendiri.');
            return;
        }

        $this->deleteUserId = $userId;
        $this->confirmingDelete = true;
    }

    public function deleteUser(): void
    {
        if (!$this->deleteUserId) {
            return;
        }

        $user = User::find($this->deleteUserId);
        if (!$user) {
            $this->notify('error', 'User tidak ditemukan.');
            $this->closeModal();
            return;
        }

        $user->delete();

        $this->notify('success', 'User berhasil dihapus.');
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->confirmingDelete = false;
        $this->deleteUserId = null;
    }

    public function clearAlert(): void
    {
        $this->alert = null;
    }

    private function notify(string $type, string $message): void
    {
        $this->alert = [
            'type' => $type,
            'message' => $message,
            'timestamp' => now()->timestamp,
        ];
    }

    public function render()
    {
        $query = User::query()
            ->with(['roles'])
            ->orderByDesc('created_at');

        if ($this->search !== '') {
            $like = '%' . trim($this->search) . '%';
            $query->where(function ($builder) use ($like) {
                $builder->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like);
            });
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->fixedRoleId) {
            $query->whereHas('roles', function ($builder) {
                $builder->where('roles.id', $this->fixedRoleId);
            });
        } elseif ($this->role !== '') {
            $query->whereHas('roles', function ($builder) {
                $builder->where('roles.id', (int) $this->role);
            });
        }

        $users = $query->paginate(10);
        $roles = Role::orderBy('name')->get();

        return view('livewire.admin.user-manager', [
            'users' => $users,
            'roles' => $roles,
            'statuses' => self::STATUSES,
        ]);
    }
}
