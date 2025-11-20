# Horizon Panel - Full Development & Production Action Plan

## Project Overview

**Goal**: Build a Laravel admin panel that manages multiple Redis connections, groups them by application, and dynamically launches Laravel Horizon UI using selected connections.

**Current Status**: 
- ✅ **Phase 1 Complete**: Database schema and Eloquent models are implemented
- ✅ **Phase 2 Complete**: Filament admin panel with CRUD operations for Applications and Redis Connections
- ⏳ **Phase 3 Pending**: Dynamic Horizon UI integration
- ⏳ **Phases 4-7 Pending**: Security, testing, production readiness, and deployment

**Technology Stack**:
- **Backend**: Laravel 12.0 (PHP 8.4)
- **Admin Panel**: Filament 4.0
- **Queue Management**: Laravel Horizon 5.0
- **Redis Client**: Predis 3.2
- **Permissions**: Spatie Laravel Permission 6.21
- **Activity Logging**: Spatie Laravel Activity Log 4.10
- **Performance**: Laravel Octane 2.13
- **Testing**: Pest 4.1
- **Frontend**: Vite 7.0, TailwindCSS 4.0
- **Deployment**: Docker Compose (MySQL 8.0 + Redis 7.2)

---

## User Review Required

> [!IMPORTANT]
> **Database Choice**: The project is currently configured to use **MySQL 8.0** via Docker Compose. This is excellent for production use with:
> - Multi-user concurrent access
> - Optimized configuration (2GB buffer pool)
> - Persistent volumes for data safety
> - Health checks for reliability

> [!WARNING]
> **Redis Connection Security**: The application will store Redis credentials (including passwords) in the database. While passwords are encrypted using Laravel's encryption, ensure:
> 1. Your `APP_KEY` is strong and never exposed
> 2. Database backups are encrypted
> 3. Access to the admin panel is restricted to trusted administrators

> [!IMPORTANT]
> **Horizon Worker Configuration**: Your Docker setup uses Supervisor to manage processes. We'll add Horizon workers to run alongside nginx and php-fpm in the same container.

---

## Proposed Changes

### Phase 3: Dynamic Horizon UI

#### [NEW] [HorizonDashboard.php](file:///var/www/html/afa/up/horizon-panel/app/Filament/Pages/HorizonDashboard.php)

Create a custom Filament page with:
- Dropdown to select an Application
- Dynamic dropdown to select a Redis Connection (filtered by selected Application)
- "Launch Horizon" button that:
  - Stores selected connection ID in session
  - Redirects to `/horizon` with dynamic configuration

**v4 Best Practices**:
- Use `Heroicon::CpuChip` for navigation icon
- Delegate form logic to `HorizonDashboardForm` class

#### [NEW] [HorizonDashboardForm.php](file:///var/www/html/afa/up/horizon-panel/app/Filament/Pages/Schemas/HorizonDashboardForm.php)

**Key Features**:
```php
public static function configure(Schema $schema): Schema
{
    return $schema->components([
        Select::make('application_id')
            ->label('Application')
            ->options(Application::pluck('name', 'id'))
            ->reactive()
            ->afterStateUpdated(fn ($set) => $set('redis_connection_id', null)),

        Select::make('redis_connection_id')
            ->label('Redis Connection')
            ->options(function (callable $get) {
                $applicationId = $get('application_id');
                if (!$applicationId) return [];
                
                return Application::find($applicationId)
                    ->redisConnections()
                    ->pluck('name', 'id');
            }),
    ]);
}
```

#### [NEW] [DynamicHorizonMiddleware.php](file:///var/www/html/afa/up/horizon-panel/app/Http/Middleware/DynamicHorizonMiddleware.php)

Middleware to intercept `/horizon` requests and:
- Retrieve selected Redis connection from session
- Dynamically configure `config('database.redis.horizon')`
- Set Horizon to use the dynamic connection
- Handle cases where no connection is selected

**Core Logic**:
```php
public function handle($request, Closure $next)
{
    $connectionId = session('selected_redis_connection_id');
    
    if ($connectionId) {
        $connection = RedisConnection::find($connectionId);
        
        if ($connection) {
            config([
                'database.redis.horizon' => [
                    'host' => $connection->host,
                    'port' => $connection->port,
                    'password' => $connection->password,
                    'database' => $connection->database,
                ],
                'horizon.use' => 'horizon',
            ]);
        }
    }
    
    return $next($request);
}
```

#### [MODIFY] [web.php](file:///var/www/html/afa/up/horizon-panel/routes/web.php)

Add routes for:
- Horizon dashboard page (handled by Filament)
- Apply middleware to `/horizon` routes

```php
Route::middleware(['web', DynamicHorizonMiddleware::class])
    ->group(function () {
        Route::get('/horizon/{path?}', function () {
            return redirect('/admin');
        })->where('path', '.*');
    });
```

#### [NEW] [horizon.php](file:///var/www/html/afa/up/horizon-panel/config/horizon.php)

Publish and configure Horizon settings:
```bash
php artisan vendor:publish --tag=horizon-config
```

Configure:
- Set default Redis connection
- Configure environments (production, local)
- Set up worker balancing strategies
- Configure metrics retention

---

### Phase 4: Security & Authentication

#### [MODIFY] [AdminPanelProvider.php](file:///var/www/html/afa/up/horizon-panel/app/Providers/Filament/AdminPanelProvider.php)

Configure:
- Authentication requirements
- User authorization policies
- Role-based access control integration

```php
->authMiddleware([
    Authenticate::class,
])
->authGuard('web')
```

#### [NEW] [HorizonServiceProvider.php](file:///var/www/html/afa/up/horizon-panel/app/Providers/HorizonServiceProvider.php)

Set up Horizon authorization:
```php
Horizon::auth(function ($request) {
    return Auth::check() && Auth::user()->can('view-horizon');
});
```

Integration with:
- Spatie Permission package
- Activity logging for Horizon access

#### [NEW] [CreateRolesAndPermissions.php](file:///var/www/html/afa/up/horizon-panel/database/seeders/CreateRolesAndPermissions.php)

Create seeder for:
- **Admin role** with full permissions:
  - `manage-applications`
  - `manage-redis-connections`
  - `view-horizon`
  - `manage-users`
- **Viewer role** with read-only access:
  - `view-applications`
  - `view-redis-connections`
  - `view-horizon`

#### [MODIFY] [User.php](file:///var/www/html/afa/up/horizon-panel/app/Models/User.php)

Add:
```php
use HasRoles;
use LogsActivity;

protected static function boot()
{
    parent::boot();
    
    static::created(function ($user) {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('User created');
    });
}
```

---

### Phase 5: Testing & Quality Assurance

#### [NEW] [ApplicationTest.php](file:///var/www/html/afa/up/horizon-panel/tests/Feature/ApplicationTest.php)

Feature tests for:
```php
test('can create application', function () {
    $this->actingAs(User::factory()->create())
        ->post('/admin/applications', [
            'name' => 'Test App',
        ])
        ->assertSuccessful();
    
    expect(Application::where('name', 'Test App')->exists())->toBeTrue();
});

test('can attach redis connection to application', function () {
    $app = Application::factory()->create();
    $redis = RedisConnection::factory()->create();
    
    $app->redisConnections()->attach($redis);
    
    expect($app->redisConnections)->toHaveCount(1);
});
```

#### [NEW] [RedisConnectionTest.php](file:///var/www/html/afa/up/horizon-panel/tests/Feature/RedisConnectionTest.php)

Feature tests for:
```php
test('password is encrypted', function () {
    $connection = RedisConnection::create([
        'name' => 'Test Redis',
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => 'secret123',
        'database' => 0,
    ]);
    
    // Password should be encrypted in database
    $raw = DB::table('redis_connections')
        ->where('id', $connection->id)
        ->value('password');
    
    expect($raw)->not->toBe('secret123');
    expect($connection->password)->toBe('secret123');
});

test('can validate redis connection', function () {
    $connection = RedisConnection::factory()->create([
        'host' => '127.0.0.1',
        'port' => 6379,
    ]);
    
    $isValid = $connection->testConnection();
    
    expect($isValid)->toBeTrue();
});
```

#### [NEW] [HorizonDashboardTest.php](file:///var/www/html/afa/up/horizon-panel/tests/Feature/HorizonDashboardTest.php)

Feature tests for:
```php
test('can select application and connection', function () {
    $app = Application::factory()->create();
    $redis = RedisConnection::factory()->create();
    $app->redisConnections()->attach($redis);
    
    $this->actingAs(User::factory()->create())
        ->post('/admin/horizon-dashboard', [
            'application_id' => $app->id,
            'redis_connection_id' => $redis->id,
        ])
        ->assertSessionHas('selected_redis_connection_id', $redis->id);
});
```

#### [NEW] [ApplicationModelTest.php](file:///var/www/html/afa/up/horizon-panel/tests/Unit/ApplicationModelTest.php)

Unit tests for model relationships and behavior.

#### [NEW] [RedisConnectionModelTest.php](file:///var/www/html/afa/up/horizon-panel/tests/Unit/RedisConnectionModelTest.php)

Unit tests for encryption and validation.

---

### Phase 6: Production Readiness

#### [NEW] [.env.production.example](file:///var/www/html/afa/up/horizon-panel/.env.production.example)

Production environment template:
```env
APP_NAME="Horizon Panel"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:GENERATE_NEW_KEY_HERE

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=horizon_panel
DB_USERNAME=user
DB_PASSWORD=STRONG_PASSWORD_HERE

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PORT=6379

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

HORIZON_PREFIX=horizon:
```

#### [MODIFY] [supervisord.conf](file:///var/www/html/afa/up/horizon-panel/compose/nginx/supervisord.conf)

Add Horizon worker process to existing supervisor configuration:
```ini
[program:horizon]
command=/usr/local/bin/php /app/artisan horizon
autostart=true
autorestart=true
user=appuser
redirect_stderr=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
stopwaitsecs=3600
```

#### [MODIFY] [database.php](file:///var/www/html/afa/up/horizon-panel/config/database.php)

Add indexes via migration:
```php
Schema::table('applications', function (Blueprint $table) {
    $table->index('name');
});

Schema::table('redis_connections', function (Blueprint $table) {
    $table->index('name');
    $table->index(['host', 'port']);
});
```

#### [NEW] [HealthCheckController.php](file:///var/www/html/afa/up/horizon-panel/app/Http/Controllers/HealthCheckController.php)

Health check endpoints:
```php
public function index()
{
    return response()->json([
        'status' => 'healthy',
        'database' => $this->checkDatabase(),
        'redis' => $this->checkRedis(),
        'horizon' => $this->checkHorizon(),
    ]);
}
```

---

### Phase 7: Documentation & Deployment

#### [MODIFY] [README.md](file:///var/www/html/afa/up/horizon-panel/README.md)

Update with:
- Project description and purpose
- Installation instructions
- Configuration guide
- Usage examples
- Screenshots of admin panel

#### [NEW] [DEPLOYMENT.md](file:///var/www/html/afa/up/horizon-panel/DEPLOYMENT.md)

Deployment guide covering:
- Server requirements
- Docker Compose setup
-    // Navigation Configuration
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::ServerStack; // MUST match parent signature (BackedEnum)res
- Environment configuration
- Database setup and migrations
- SSL/TLS configuration
- Backup and restore procedures

#### [NEW] [TROUBLESHOOTING.md](file:///var/www/html/afa/up/horizon-panel/TROUBLESHOOTING.md)

Common issues and solutions:
- Redis connection failures
- Horizon worker issues
- Permission problems
- Database migration errors

#### [EXISTING] [Dockerfile](file:///var/www/html/afa/up/horizon-panel/compose/nginx/Dockerfile)

Your existing Docker setup already includes:
- ✅ PHP 8.4-FPM on Alpine
- ✅ Nginx web server
- ✅ Supervisor for process management
- ✅ Redis PHP extension (phpredis)
- ✅ All required PHP extensions
- ⚠️ **Needs**: Horizon worker process added to supervisord.conf

#### [EXISTING] [docker-compose.yml](file:///var/www/html/afa/up/horizon-panel/docker-compose.yml)

Your existing Docker Compose setup includes:
- ✅ Application container with Nginx + PHP-FPM
- ✅ MySQL 8.0 database (optimized configuration)
- ✅ Redis 7.2 (optimized configuration)
- ✅ Health checks for all services
- ✅ Persistent volumes for data
- ⚠️ **Needs**: Horizon worker added to app container via supervisord

---

## Verification Plan

### Automated Tests

1. **Run Pest Test Suite**:
   ```bash
   docker-compose exec app php artisan test
   ```

2. **Code Quality Checks**:
   ```bash
   docker-compose exec app ./vendor/bin/pint --test
   ```

3. **Test Coverage** (optional):
   ```bash
   docker-compose exec app php artisan test --coverage --min=80
   ```

### Manual Verification

1. **Admin Panel Testing**:
   - Create multiple applications
   - Create multiple Redis connections
   - Attach connections to applications
   - Verify relationship management

2. **Dynamic Horizon Testing**:
   - Select an application and connection
   - Launch Horizon dashboard
   - Verify Horizon connects to correct Redis instance
   - Test with multiple different connections
   - Verify session persistence

3. **Security Testing**:
   - Test authentication requirements
   - Verify role-based access control
   - Test password encryption/decryption
   - Verify activity logging

4. **Production Environment Testing**:
   - Deploy to staging environment
   - Run migrations
   - Start queue workers
   - Monitor Horizon dashboard
   - Test under load
   - Verify error logging
   - Test backup and restore

### Performance Testing

1. **Database Query Optimization**:
   - Use Laravel Debugbar to identify N+1 queries
   - Add appropriate indexes
   - Test with large datasets

2. **Redis Connection Testing**:
   - Test connection pooling
   - Verify timeout settings
   - Test failover scenarios

3. **Queue Performance**:
   - Test with high job volume
   - Monitor worker memory usage
   - Verify job processing times

---

## Docker Compose Deployment Notes

> [!NOTE]
> **Your Current Setup**: You're already using Docker Compose with MySQL 8.0 and Redis 7.2. The existing configuration is production-optimized with:
> - Health checks for all services
> - Optimized MySQL and Redis configurations
> - Persistent volumes for data
> - Proper networking

**Required Changes for Horizon**:
1. Add Horizon worker to `supervisord.conf`
2. Publish Horizon assets during build
3. Ensure Horizon can connect to both local Redis (for queues) and dynamic Redis connections (for monitoring)

**Deployment Commands**:
```bash
# Build and start services
docker-compose up -d --build

# Run migrations
docker-compose exec app php artisan migrate --force

# Create admin user
docker-compose exec app php artisan make:filament-user

# Check Horizon status
docker-compose exec app php artisan horizon:status

# View logs
docker-compose logs -f app
```

---

## Next Steps

1. ✅ **Confirmed**: Using Docker Compose with MySQL 8.0
2. **Phase 3**: Implement Dynamic Horizon UI functionality
3. **Phase 4**: Set up security and authentication
4. **Phase 5**: Write comprehensive tests
5. **Phase 6**: Prepare for production deployment (Docker-specific)
6. **Phase 7**: Create documentation and deploy

---

## Implementation Timeline

**Estimated Total Time**: 3-5 days (depending on testing depth and deployment complexity)

- **Phase 3** (Dynamic Horizon UI): 4-6 hours
- **Phase 4** (Security & Authentication): 3-4 hours
- **Phase 5** (Testing): 6-8 hours
- **Phase 6** (Production Readiness): 4-6 hours
- **Phase 7** (Documentation & Deployment): 3-4 hours

---

## Dependencies & Prerequisites

### Development Environment
- PHP 8.4+
- Composer 2.x
- Node.js 18+ and npm
- Redis server
- Database (MySQL via Docker)

### Production Environment (Docker Compose)
- ✅ Docker Engine 20.10+
- ✅ Docker Compose v2+
- SSL/TLS certificates (for HTTPS)
- Reverse proxy (Nginx/Traefik) for SSL termination (optional)
- Monitoring tools (optional: New Relic, Sentry)
- Backup solution for MySQL volumes

### Required PHP Extensions
- PDO
- Redis (phpredis or predis)
- OpenSSL
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
