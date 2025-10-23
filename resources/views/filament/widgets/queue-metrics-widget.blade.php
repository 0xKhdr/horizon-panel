<x-filament-widgets::widget>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Queue Configurations -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Queue Configurations</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalConfigurations }}</p>
                </div>
                <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900">
                    <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Configurations -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active</p>
                    <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ $activeConfigurations }}</p>
                </div>
                <div class="rounded-full bg-green-100 p-3 dark:bg-green-900">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Processes -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Max Processes</p>
                    <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $totalProcesses }}</p>
                </div>
                <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Applications with Queues -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Applications</p>
                    <p class="text-2xl font-semibold text-indigo-600 dark:text-indigo-400">{{ $applicationsWithQueues }}</p>
                </div>
                <div class="rounded-full bg-indigo-100 p-3 dark:bg-indigo-900">
                    <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @if($activeConfigurations == 0)
    <div class="mt-6 rounded-xl bg-yellow-50 p-4 dark:bg-yellow-900/20">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">No Active Queue Configurations</h3>
                <p class="text-sm text-yellow-700 dark:text-yellow-300">Create queue configurations to start processing jobs with Horizon.</p>
            </div>
        </div>
    </div>
    @endif
</x-filament-widgets::widget>
