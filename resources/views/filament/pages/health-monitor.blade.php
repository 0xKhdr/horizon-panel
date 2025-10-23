<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Health Monitor</h1>
                <p class="text-gray-600 dark:text-gray-400">Monitor Redis connections and system health in real-time</p>
            </div>
            <div class="flex items-center space-x-2">
                <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                    <div class="h-2 w-2 rounded-full bg-green-500"></div>
                    <span>Last updated: {{ now()->format('M j, H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Redis Connections Health -->
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Redis Health</p>
                        <p class="text-2xl font-semibold text-green-600 dark:text-green-400">98.5%</p>
                    </div>
                    <div class="rounded-full bg-green-100 p-3 dark:bg-green-900">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Processes -->
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Processes</p>
                        <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">12</p>
                    </div>
                    <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Queue Throughput -->
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jobs/min</p>
                        <p class="text-2xl font-semibold text-purple-600 dark:text-purple-400">1,247</p>
                    </div>
                    <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900">
                        <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- System Load -->
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">System Load</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">45%</p>
                    </div>
                    <div class="rounded-full bg-gray-100 p-3 dark:bg-gray-900">
                        <svg class="h-6 w-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Connection Status Table -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Redis Connection Status</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Real-time health monitoring of all Redis connections</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Sample connection status rows -->
                    <div class="flex items-center justify-between py-3 px-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                        <div class="flex items-center space-x-3">
                            <div class="h-3 w-3 rounded-full bg-green-500"></div>
                            <div>
                                <p class="font-medium text-green-900 dark:text-green-100">Production Redis</p>
                                <p class="text-sm text-green-700 dark:text-green-300">redis://prod-cluster:6379</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-green-900 dark:text-green-100">2.3ms</p>
                            <p class="text-xs text-green-700 dark:text-green-300">12.5MB used</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between py-3 px-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <div class="flex items-center space-x-3">
                            <div class="h-3 w-3 rounded-full bg-red-500"></div>
                            <div>
                                <p class="font-medium text-red-900 dark:text-red-100">Staging Redis</p>
                                <p class="text-sm text-red-700 dark:text-red-300">redis://staging:6379</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-red-900 dark:text-red-100">Timeout</p>
                            <p class="text-xs text-red-700 dark:text-red-300">Connection failed</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between py-3 px-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800">
                        <div class="flex items-center space-x-3">
                            <div class="h-3 w-3 rounded-full bg-yellow-500"></div>
                            <div>
                                <p class="font-medium text-yellow-900 dark:text-yellow-100">Development Redis</p>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">redis://localhost:6379</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-yellow-900 dark:text-yellow-100">15.7ms</p>
                            <p class="text-xs text-yellow-700 dark:text-yellow-300">High latency</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Latest system events and connection tests</p>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center space-x-3 text-sm">
                        <div class="h-2 w-2 rounded-full bg-green-500"></div>
                        <span class="text-gray-600 dark:text-gray-400">Production Redis connection test passed</span>
                        <span class="text-gray-400 dark:text-gray-500">2 minutes ago</span>
                    </div>
                    <div class="flex items-center space-x-3 text-sm">
                        <div class="h-2 w-2 rounded-full bg-red-500"></div>
                        <span class="text-gray-600 dark:text-gray-400">Staging Redis connection failed</span>
                        <span class="text-gray-400 dark:text-gray-500">5 minutes ago</span>
                    </div>
                    <div class="flex items-center space-x-3 text-sm">
                        <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                        <span class="text-gray-600 dark:text-gray-400">Queue configuration updated for App A</span>
                        <span class="text-gray-400 dark:text-gray-500">10 minutes ago</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
