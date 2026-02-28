<div class="space-y-6">
    @if (session()->has('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-600 dark:bg-green-900/80 dark:text-green-100 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-600 dark:bg-red-900/80 dark:text-red-100 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Antrian Dispute</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Kelola dispute yang sedang terbuka / investigasi.</p>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    <tr>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Order</th>
                        <th class="px-6 py-3 text-left">Customer</th>
                        <th class="px-6 py-3 text-left">Agency</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Dibuat</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($disputes as $dispute)
                        @php
                            $statusColors = [
                                'open' => 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200',
                                'investigating' => 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-200',
                                'resolved' => 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-200',
                                'rejected' => 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-200',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50">
                            <td class="px-6 py-4 font-mono text-gray-900 dark:text-slate-50">#{{ $dispute->id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-mono text-gray-900 dark:text-slate-50">#{{ $dispute->order_id }}</div>
                                <div class="text-xs text-gray-500 dark:text-slate-300">{{ optional($dispute->order)->code ?? '' }}</div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-slate-300">{{ \Illuminate\Support\Str::limit($dispute->complaint, 80) }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-200">{{ optional(optional($dispute->order)->visitor)->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-200">{{ optional(optional($dispute->order)->agency)->company_name ?? optional(optional($dispute->order)->agency)->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusColors[$dispute->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-200' }}">
                                    {{ \App\Domain\Shared\Statuses\DisputeStatus::label($dispute->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-300">{{ $dispute->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.disputes.show', $dispute->id) }}" class="inline-flex items-center gap-1 rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition-colors">
                                    Detail
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-slate-300">
                                Tidak ada dispute yang perlu ditangani.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $disputes->links() }}</div>
    </div>
</div>
