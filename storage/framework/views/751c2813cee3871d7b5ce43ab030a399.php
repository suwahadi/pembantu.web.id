<div class="min-h-screen bg-gray-50">
    <!-- Search Header -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-4xl font-bold mb-6">Cari Jasa Profesional</h1>
            <div class="flex gap-3">
                <input 
                    type="text" 
                    wire:model.live.debounce-500ms="query"
                    placeholder="Nama pekerja atau jasa..." 
                    class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-300"
                >
                <button class="px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                    Cari
                </button>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-4 gap-6">
            <!-- Sidebar Filters -->
            <div class="col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Filter</h3>

                    <!-- Category Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select wire:model.live="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>

                    <!-- Location Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                        <select wire:model.live="location_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Lokasi</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($loc->id); ?>"><?php echo e($loc->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Range Harga (IDR)</label>
                        <div class="space-y-2">
                            <input 
                                type="number" 
                                wire:model.live.debounce-500ms="min_price"
                                placeholder="Min" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            <input 
                                type="number" 
                                wire:model.live.debounce-500ms="max_price"
                                placeholder="Max" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="col-span-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results->isEmpty()): ?>
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <p class="text-gray-600 text-lg">Tidak ada hasil pencarian. Silakan coba dengan kata kunci lain.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $worker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="/worker/<?php echo e($worker->id); ?>" class="group">
                                <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 flex gap-6">
                                    <!-- Worker Avatar -->
                                    <div class="w-24 h-24 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-3xl font-bold"><?php echo e(substr($worker->name, 0, 1)); ?></span>
                                    </div>

                                    <!-- Worker Info -->
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition"><?php echo e($worker->name); ?></h3>
                                                <p class="text-sm text-gray-600"><?php echo e($worker->category->name); ?></p>
                                            </div>
                                            <div class="text-right">
                                                <div class="flex items-center justify-end space-x-1 mb-1">
                                                    <span class="text-yellow-500">
                                                        <?php echo $__env->make('svgs.icon-star', ['class' => 'w-4 h-4'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                                    </span>
                                                    <span class="font-semibold text-gray-900"><?php echo e(number_format($worker->rating, 1)); ?></span>
                                                    <span class="text-xs text-gray-500">(<?php echo e($worker->total_reviews); ?>)</span>
                                                </div>
                                                <p class="text-sm text-gray-600"><?php echo e($worker->total_completed_orders); ?> pesanan selesai</p>
                                            </div>
                                        </div>

                                        <!-- Bio -->
                                        <p class="text-sm text-gray-600 mb-3"><?php echo e(Str::limit($worker->bio, 100)); ?></p>

                                        <!-- Location & Price -->
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center space-x-1 text-sm text-gray-600">
                                                <?php echo $__env->make('svgs.icon-location', ['class' => 'w-4 h-4'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                                <span><?php echo e($worker->location->name); ?></span>
                                            </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($worker->pricings->first()): ?>
                                                <p class="text-xl font-bold text-blue-600">
                                                    Rp <?php echo e(number_format($worker->pricings->first()->price_idr, 0, ',', '.')); ?>

                                                </p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="mt-8 text-center">
                        <p class="text-gray-600">Menampilkan <?php echo e($results->count()); ?> hasil</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\pembantu.web.id\resources\views/livewire/pages/search.blade.php ENDPATH**/ ?>