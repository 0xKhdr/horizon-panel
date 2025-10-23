<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Application permissions
            'applications.view',
            'applications.create',
            'applications.edit',
            'applications.delete',

            // Redis Connection permissions
            'redis-connections.view',
            'redis-connections.create',
            'redis-connections.edit',
            'redis-connections.delete',
            'redis-connections.test',

            // Queue Configuration permissions
            'queue-configurations.view',
            'queue-configurations.create',
            'queue-configurations.edit',
            'queue-configurations.delete',

            // Health Monitoring permissions
            'health-monitoring.view',
            'health-monitoring.refresh',

            // Activity Log permissions
            'activity-logs.view',

            // System permissions
            'system.settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'applications.view',
            'applications.create',
            'applications.edit',
            'applications.delete',
            'redis-connections.view',
            'redis-connections.create',
            'redis-connections.edit',
            'redis-connections.delete',
            'redis-connections.test',
            'queue-configurations.view',
            'queue-configurations.create',
            'queue-configurations.edit',
            'queue-configurations.delete',
            'health-monitoring.view',
            'health-monitoring.refresh',
            'activity-logs.view',
        ]);

        $operatorRole = Role::firstOrCreate(['name' => 'operator']);
        $operatorRole->givePermissionTo([
            'applications.view',
            'redis-connections.view',
            'redis-connections.test',
            'queue-configurations.view',
            'health-monitoring.view',
            'health-monitoring.refresh',
            'activity-logs.view',
        ]);

        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);
        $viewerRole->givePermissionTo([
            'applications.view',
            'redis-connections.view',
            'queue-configurations.view',
            'health-monitoring.view',
            'activity-logs.view',
        ]);

        // Create a default super admin user if it doesn't exist
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@horizon.local'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('super-admin');

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Default admin user created: admin@horizon.local / password');
    }
}
