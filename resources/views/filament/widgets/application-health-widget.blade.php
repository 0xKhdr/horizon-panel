<x-filament-widgets::widget>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Applications -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Applications</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalApplications }}</p>
                </div>
                <div class="rounded-full bg-gray-100 p-3 dark:bg-gray-900">
                    <svg class="h-6 w-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Applications -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active</p>
                    <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ $activeApplications }}</p>
                </div>
                <div class="rounded-full bg-green-100 p-3 dark:bg-green-900">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- With Redis Connections -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">With Redis</p>
                    <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $applicationsWithConnections }}</p>
                </div>
                <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Without Redis Connections -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Need Setup</p>
                    <p class="text-2xl font-semibold {{ $applicationsWithoutConnections > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }}">
                        {{ $applicationsWithoutConnections }}
                    </p>
                </div>
                <div class="rounded-full {{ $applicationsWithoutConnections > 0 ? 'bg-orange-100 dark:bg-orange-900' : 'bg-green-100 dark:bg-green-900' }} p-3">
                    <svg class="h-6 w-6 {{ $applicationsWithoutConnections > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @if($applicationsWithoutConnections > 0)
    <div class="mt-6 rounded-xl bg-orange-50 p-4 dark:bg-orange-900/20">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-orange-800 dark:text-orange-200">Applications Need Redis Setup</h3>
                <p class="text-sm text-orange-700 dark:text-orange-300">{{ $applicationsWithoutConnections }} application(s) need Redis connections configured to process queues.</p>
            </div>
        </div>
    </div>
    @endif
</x-filament-widgets::widget>
