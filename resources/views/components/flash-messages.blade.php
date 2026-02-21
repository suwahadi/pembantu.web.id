@if(session('info'))
  <div class="fixed top-4 right-4 z-50 max-w-sm">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 shadow-lg dark:bg-blue-900/20 dark:border-blue-800">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-blue-800 dark:text-blue-200">{{ session('info') }}</p>
        </div>
      </div>
    </div>
  </div>
@endif

@if(session('error'))
  <div class="fixed top-4 right-4 z-50 max-w-sm">
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg dark:bg-red-900/20 dark:border-red-800">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
      </div>
    </div>
  </div>
@endif

@if(session('success'))
  <div class="fixed top-4 right-4 z-50 max-w-sm">
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg dark:bg-green-900/20 dark:border-green-800">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
      </div>
    </div>
  </div>
@endif

<script>
  // Auto-hide flash messages after 5 seconds
  document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('[class*="fixed top-4 right-4"]');
    alerts.forEach(function(alert) {
      setTimeout(function() {
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(100%)';
        alert.style.transition = 'all 0.3s ease-out';
        setTimeout(function() {
          alert.remove();
        }, 300);
      }, 5000);
    });
  });
</script>
