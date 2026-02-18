# Implementasi UI Search & Worker Detail (Livewire + Blade) dengan Dark/Light Toggle + Worker Public Code

Dokumen ini berisi:
1) Implementasi **Blade layout** dengan toggle **dark/light mode**
2) Implementasi **/search** (Livewire) + querystring: `/search?category=sopir&location=jakarta&sort=price_asc`
3) Implementasi **/worker/{publicId}** (Livewire) contoh: `/worker/hdgy65rv`
4) Komponen icon **SVG** (tanpa emoji)
5) **Update migration** untuk `workers.public_id` + `slug` kategori/lokasi (opsional tapi direkomendasikan)
6) Arahan **service layer** yang ketat agar `public_id` aman dari duplicate (race-safe)

> Catatan: Semua label/text Bahasa Indonesia. Semua format uang IDR. UI responsive & kompatibel dark/light mode.

---

## A) Update Tailwind untuk Dark Mode

### A.1 `tailwind.config.js`
Pastikan dark mode berbasis class:
```js
export default {
  darkMode: 'class',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Livewire/**/*.php',
  ],
  theme: { extend: {} },
  plugins: [],
}
```

---

## B) Theme Toggle (JS) — Dark/Light

### B.1 `resources/js/theme.js`
```js
(function () {
  const key = 'theme';
  const root = document.documentElement;

  function apply(theme) {
    if (theme === 'dark') root.classList.add('dark');
    else root.classList.remove('dark');
  }

  const saved = localStorage.getItem(key);
  if (saved === 'dark' || saved === 'light') {
    apply(saved);
  } else {
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    apply(prefersDark ? 'dark' : 'light');
  }

  window.__toggleTheme = function () {
    const isDark = root.classList.contains('dark');
    const next = isDark ? 'light' : 'dark';
    localStorage.setItem(key, next);
    apply(next);
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: next } }));
  };
})();
```

### B.2 Import di `resources/js/app.js`
```js
import './bootstrap';
import './theme';
```

---

## C) Layout App: Header + Toggle + Dark/Light

### C.1 `resources/views/layouts/app.blade.php`
```blade
<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Pembantu.web.id' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  @livewireStyles
</head>
<body class="min-h-full bg-white text-gray-900 dark:bg-gray-950 dark:text-gray-100">
  <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/80 backdrop-blur dark:border-gray-800 dark:bg-gray-950/80">
    <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between gap-3">
      <a href="{{ url('/') }}" class="flex items-center gap-2">
        <div class="h-9 w-9 rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900 flex items-center justify-center">
          <x-icon.logo class="h-5 w-5" />
        </div>
        <div>
          <div class="font-semibold leading-tight">Pembantu.web.id</div>
          <div class="text-xs text-gray-600 dark:text-gray-400">Portal jasa tenaga kerja</div>
        </div>
      </a>

      <nav class="flex items-center gap-2">
        <a href="{{ route('search') }}"
           class="hidden sm:inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm
                  border-gray-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-900">
          <x-icon.search class="h-4 w-4" />
          <span>Cari</span>
        </a>

        <button type="button" onclick="window.__toggleTheme()"
          class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm
                 border-gray-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-900"
          aria-label="Toggle dark mode">
          <span class="hidden dark:inline">
            <x-icon.sun class="h-4 w-4" />
          </span>
          <span class="inline dark:hidden">
            <x-icon.moon class="h-4 w-4" />
          </span>
          <span class="hidden sm:inline">Mode</span>
        </button>
      </nav>
    </div>
  </header>

  <main class="mx-auto max-w-6xl px-4 py-6">
    {{ $slot }}
  </main>

  <footer class="border-t border-gray-200 dark:border-gray-800">
    <div class="mx-auto max-w-6xl px-4 py-6 text-sm text-gray-600 dark:text-gray-400">
      © {{ date('Y') }} Pembantu.web.id
    </div>
  </footer>

  @livewireScripts
</body>
</html>
```

---

## D) SVG Icon Components (Tanpa Emoji)

Buat folder: `resources/views/components/icon/`

### D.1 `icon/search.blade.php`
```blade
@props(['class' => 'h-5 w-5'])
<svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
  <path d="M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" />
  <path d="M16.5 16.5 21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
</svg>
```

### D.2 `icon/map-pin.blade.php`
```blade
@props(['class' => 'h-5 w-5'])
<svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
  <path d="M12 22s7-4.5 7-12a7 7 0 1 0-14 0c0 7.5 7 12 7 12Z" stroke="currentColor" stroke-width="2"/>
  <path d="M12 13.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="2"/>
</svg>
```

### D.3 `icon/sort.blade.php`
```blade
@props(['class' => 'h-5 w-5'])
<svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
  <path d="M7 7h10M7 12h7M7 17h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
</svg>
```

### D.4 `icon/arrow-left.blade.php`
```blade
@props(['class' => 'h-5 w-5'])
<svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
  <path d="M15 18 9 12l6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
```

### D.5 `icon/sun.blade.php`
```blade
@props(['class' => 'h-5 w-5'])
<svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
  <path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Z" stroke="currentColor" stroke-width="2"/>
  <path d="M12 2v2M12 20v2M4 12H2M22 12h-2M5 5 3.6 3.6M20.4 20.4 19 19M19 5l1.4-1.4M3.6 20.4 5 19"
        stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
</svg>
```

### D.6 `icon/moon.blade.php`
```blade
@props(['class' => 'h-5 w-5'])
<svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
  <path d="M21 13.2A8.5 8.5 0 0 1 10.8 3 7.5 7.5 0 1 0 21 13.2Z" stroke="currentColor" stroke-width="2" />
</svg>
```

### D.7 `icon/logo.blade.php`
```blade
@props(['class' => 'h-5 w-5'])
<svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
  <path d="M6 8c0-2 1.5-4 4-4h4c2.5 0 4 2 4 4v8c0 2-1.5 4-4 4h-4c-2.5 0-4-2-4-4V8Z"
        stroke="currentColor" stroke-width="2"/>
  <path d="M9 10h6M9 14h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
</svg>
```

---

## E) Routes yang Diharapkan

### E.1 `routes/web.php`
```php
use App\Livewire\Public\WorkerSearchPage;
use App\Livewire\Public\WorkerShowPage;

Route::get('/search', WorkerSearchPage::class)->name('search');
Route::get('/worker/{publicId}', WorkerShowPage::class)->name('worker.show');
```

---

## F) Update Migration: Worker Public Code (`workers.public_id`)

### F.1 Migration add `public_id` + unique index
`database/migrations/2026_02_18_000001_add_public_id_to_workers.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->string('public_id', 16)->nullable()->after('id');
            $table->unique('public_id');
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropUnique(['public_id']);
            $table->dropColumn('public_id');
        });
    }
};
```

### F.2 Backfill worker lama (command)
`app/Console/Commands/BackfillWorkerPublicId.php`
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Domain\Worker\Services\WorkerPublicIdService;

final class BackfillWorkerPublicId extends Command
{
    protected $signature = 'workers:backfill-public-id {--limit=500}';
    protected $description = 'Mengisi public_id pada workers yang masih null';

    public function handle(WorkerPublicIdService $ids): int
    {
        $limit = (int)$this->option('limit');

        $rows = DB::table('workers')
            ->whereNull('public_id')
            ->orderBy('id')
            ->limit($limit)
            ->get(['id']);

        if ($rows->isEmpty()) {
            $this->info('Tidak ada data yang perlu di-backfill.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($rows, $ids) {
            foreach ($rows as $r) {
                $code = $ids->generateUnique();
                DB::table('workers')->where('id', (int)$r->id)->update([
                    'public_id' => $code,
                    'updated_at' => now(),
                ]);
            }
        });

        $this->info('Selesai backfill: '.$rows->count().' worker.');
        return self::SUCCESS;
    }
}
```

---

## G) Service Layer Ketat: Generator `public_id` anti duplicate (race-safe)

### G.1 Prinsip wajib
- **Unique index** di DB adalah proteksi utama.
- Generator melakukan retry bila collision / duplicate key.
- `public_id` hanya digenerate dari **service**, bukan dari UI.

### G.2 `app/Domain/Worker/Services/WorkerPublicIdService.php`
```php
<?php

namespace App\Domain\Worker\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class WorkerPublicIdService
{
    public function generateCandidate(int $length = 8): string
    {
        $pool = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $out = '';
        for ($i=0; $i<$length; $i++) {
            $out .= $pool[random_int(0, strlen($pool)-1)];
        }
        return $out;
    }

    public function generateUnique(int $length = 8, int $maxRetry = 25): string
    {
        for ($i=0; $i<$maxRetry; $i++) {
            $code = $this->generateCandidate($length);
            if (!DB::table('workers')->where('public_id', $code)->exists()) {
                return $code;
            }
        }
        throw new RuntimeException('Gagal menghasilkan public_id unik. Coba naikkan length.');
    }

    public function insertWorkerWithUniquePublicId(callable $insertFn, int $length = 8, int $maxRetry = 25)
    {
        for ($i=0; $i<$maxRetry; $i++) {
            $publicId = $this->generateCandidate($length);

            try {
                return $insertFn($publicId);
            } catch (QueryException $e) {
                if (str_contains($e->getMessage(), 'Duplicate') || (int)($e->errorInfo[1] ?? 0) === 1062) {
                    continue;
                }
                throw $e;
            }
        }
        throw new RuntimeException('Gagal insert worker karena public_id selalu duplicate. Coba naikkan length.');
    }
}
```

### G.3 Patch `WorkerService->create()` agar selalu isi `public_id`
```php
use App\Domain\Worker\Services\WorkerPublicIdService;

public function create(int $agencyId, array $data): object
{
    return DB::transaction(function () use ($agencyId, $data) {
        $ids = app(WorkerPublicIdService::class);

        return $ids->insertWorkerWithUniquePublicId(function (string $publicId) use ($agencyId, $data) {
            $id = DB::table('workers')->insertGetId([
                'public_id' => $publicId,
                'agency_id' => $agencyId,
                'category_id' => (int)$data['category_id'],
                'location_id' => $data['location_id'] ? (int)$data['location_id'] : null,
                'name' => trim($data['name']),
                'bio' => trim($data['bio'] ?? ''),
                'skills' => trim($data['skills'] ?? ''),
                'default_scheme' => $data['default_scheme'] ?? 'BULANAN',
                'min_price_idr' => (int)$data['min_price_idr'],
                'rank_score' => 0,
                'is_active' => (int)($data['is_active'] ?? 1),
                'photo_path' => $data['photo_path'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return DB::table('workers')->where('id', $id)->first();
        }, length: 8, maxRetry: 25);
    });
}
```

---

## H) (Opsional) Slug untuk Category & Location

Agar querystring stabil:
- `/search?category=sopir&location=jakarta&sort=price_asc`

Migration:
`database/migrations/2026_02_18_000002_add_slug_to_categories_locations.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('worker_categories', function (Blueprint $table) {
            $table->string('slug', 80)->nullable()->after('name');
            $table->unique('slug');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->string('slug', 80)->nullable()->after('name');
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('worker_categories', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
```

---

## I) Livewire: `/search`

### I.1 `app/Livewire/Public/WorkerSearchPage.php`
```php
<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Domain\Worker\Services\WorkerCatalogService;

final class WorkerSearchPage extends Component
{
    use WithPagination;

    public string $q = '';
    public string $category = ''; // slug
    public string $location = ''; // slug
    public string $sort = 'relevance';

    protected $queryString = [
        'q' => ['except' => ''],
        'category' => ['except' => ''],
        'location' => ['except' => ''],
        'sort' => ['except' => 'relevance'],
    ];

    public function updated($name): void { $this->resetPage(); }

    public function render(WorkerCatalogService $catalog)
    {
        $categoryId = $this->category !== '' ? DB::table('worker_categories')->where('slug', $this->category)->value('id') : null;
        $locationId = $this->location !== '' ? DB::table('locations')->where('slug', $this->location)->value('id') : null;

        $items = $catalog->search([
            'q' => $this->q,
            'category_id' => $categoryId,
            'location_id' => $locationId,
            'page' => $this->getPage(),
        ]);

        if ($this->sort === 'price_asc') $items->setCollection($items->getCollection()->sortBy('min_price_idr')->values());
        if ($this->sort === 'price_desc') $items->setCollection($items->getCollection()->sortByDesc('min_price_idr')->values());
        if ($this->sort === 'newest') $items->setCollection($items->getCollection()->sortByDesc('id')->values());

        $categories = DB::table('worker_categories')->orderBy('name')->get(['name','slug']);
        $locations = DB::table('locations')->orderBy('name')->get(['name','slug']);

        return view('livewire.public.worker-search-page', compact('items','categories','locations'))
            ->layout('layouts.app', ['title' => 'Cari Tenaga Kerja']);
    }
}
```

### I.2 View `resources/views/livewire/public/worker-search-page.blade.php`
(Lihat versi lengkap di bagian chat/implementasi; ini harus dipakai apa adanya.)

---

## J) Livewire: `/worker/{publicId}`

### J.1 `app/Livewire/Public/WorkerShowPage.php`
```php
<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Domain\Worker\Services\WorkerCatalogService;

final class WorkerShowPage extends Component
{
    public string $publicId;
    public ?object $worker = null;

    public function mount(string $publicId, WorkerCatalogService $catalog): void
    {
        $this->publicId = $publicId;
        $this->worker = $catalog->findPublicByPublicId($publicId);
    }

    public function render()
    {
        return view('livewire.public.worker-show-page')
            ->layout('layouts.app', ['title' => $this->worker?->name ?? 'Detail Worker']);
    }
}
```

### J.2 View `resources/views/livewire/public/worker-show-page.blade.php`
(Lihat versi lengkap di bagian chat/implementasi; ini harus dipakai apa adanya.)

---

## K) Patch WorkerCatalogService

### K.1 Pastikan `search()` select include `workers.public_id`
Tambahkan ke `select([...])`:
```php
'workers.public_id',
```

### K.2 Tambah `findPublicByPublicId()`
```php
public function findPublicByPublicId(string $publicId): ?object
{
    return DB::table('workers')
        ->join('agencies','agencies.id','=','workers.agency_id')
        ->join('worker_categories','worker_categories.id','=','workers.category_id')
        ->leftJoin('locations','locations.id','=','workers.location_id')
        ->select([
            'workers.*',
            'agencies.name as agency_name',
            'worker_categories.name as category_name',
            DB::raw('COALESCE(locations.name, "-") as location_name'),
        ])
        ->where('workers.public_id', $publicId)
        ->where('workers.is_active', 1)
        ->where('agencies.status', 'active')
        ->first();
}
```

---

## L) Checklist Implementasi

1. [ ] Migration: `workers.public_id` + unique  
2. [ ] Service: `WorkerPublicIdService` generator + retry  
3. [ ] Patch `WorkerService->create()` agar selalu set `public_id` via service  
4. [ ] Backfill worker lama (`workers:backfill-public-id`)  
5. [ ] Add routes: `/search`, `/worker/{publicId}`  
6. [ ] Implement Livewire `WorkerSearchPage`, `WorkerShowPage` + Blade views  
7. [ ] Implement layout `layouts/app.blade.php` + icons SVG  
8. [ ] Implement `theme.js` + import ke `app.js`  
9. [ ] Patch `WorkerCatalogService` select `public_id` + method find  
10. [ ] (Opsional) Slug categories/locations untuk querystring yang rapi

---

Selesai.
