<x-filament-widgets::widget>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Connections -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Connections</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalConnections }}</p>
                </div>
                <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Connections -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active</p>
                    <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ $activeConnections }}</p>
                </div>
                <div class="rounded-full bg-green-100 p-3 dark:bg-green-900">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Healthy Connections -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Healthy</p>
                    <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ $healthyConnections }}</p>
                </div>
                <div class="rounded-full bg-green-100 p-3 dark:bg-green-900">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Health Percentage -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Health Rate</p>
                    <p class="text-2xl font-semibold {{ $healthPercentage >= 80 ? 'text-green-600 dark:text-green-400' : ($healthPercentage >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                        {{ $healthPercentage }}%
                    </p>
                </div>
                <div class="rounded-full {{ $healthPercentage >= 80 ? 'bg-green-100 dark:bg-green-900' : ($healthPercentage >= 50 ? 'bg-yellow-100 dark:bg-yellow-900' : 'bg-red-100 dark:bg-red-900') }} p-3">
                    <svg class="h-6 w-6 {{ $healthPercentage >= 80 ? 'text-green-600 dark:text-green-400' : ($healthPercentage >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @if($unhealthyConnections > 0)
    <div class="mt-6 rounded-xl bg-red-50 p-4 dark:bg-red-900/20">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Attention Required</h3>
                <p class="text-sm text-red-700 dark:text-red-300">{{ $unhealthyConnections }} connection(s) need attention</p>
            </div>
        </div>
    </div>
    @endif
</x-filament-widgets::widget>
