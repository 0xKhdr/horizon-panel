# Simplified Plan: Dynamic Horizon Panel

## Objective
Create a simple admin panel to manage Redis connections, group them by application, and use them to dynamically launch the Laravel Horizon UI.

---

## Phase 1: Database and Models

### Description
Define the database schema and Eloquent models for applications and Redis connections, establishing the relationships between them.

### Tables
1.  **`applications`**
    *   `id` (PK)
    *   `name` (string)
    *   `created_at`, `updated_at`

2.  **`redis_connections`**
    *   `id` (PK)
    *   `name` (string, unique)
    *   `host` (string)
    *   `port` (integer, default 6379)
    *   `password` (text, nullable, encrypted)
    *   `database` (unsigned tiny integer, default 0)
    *   `created_at`, `updated_at`

3.  **`application_redis_connection` (Pivot Table)**
    *   `id` (PK)
    *   `application_id` (FK to `applications`)
    *   `redis_connection_id` (FK to `redis_connections`)
    *   `created_at`, `updated_at`
    *   Unique constraint on `application_id` and `redis_connection_id`

### Eloquent Models
*   `app/Models/Application.php`
*   `app/Models/RedisConnection.php`

---

## Phase 2: Admin Panel for Management (CRUD)

### Description
Implement the admin panel using Laravel Filament to provide full CRUD functionality for Applications and Redis Connections.

### Filament Resources
1.  **`ApplicationResource`**
    *   Form fields: `name`
    *   Table columns: `name`
    *   Ability to attach/detach `RedisConnection` records (e.g., using a multi-select field).

2.  **`RedisConnectionResource`**
    *   Form fields: `name`, `host`, `port`, `password`, `database`.
    *   Table columns: `name`, `host`, `port`, `database`.
    *   Ability to attach/detach `Application` records.

---

## Phase 3: Dynamic Horizon UI

### Description
Create a Filament page that allows users to select a configured Redis connection and then dynamically launch the Laravel Horizon UI using that specific connection.

### Components
1.  **`app/Filament/Pages/HorizonDashboard.php`**
    *   A Filament custom page.
    *   Contains a form with a dropdown to select an `Application`.
    *   A second dropdown, dynamically populated, to select a `RedisConnection` associated with the chosen `Application`.
    *   A "Launch Horizon" button.

2.  **Dynamic Configuration Logic**
    *   Upon selecting a connection and clicking "Launch", the system will:
        *   Store the selected `redis_connection_id` (e.g., in the session).
        *   Dynamically register the selected Redis connection details into Laravel's `config('database.redis')`.
        *   Dynamically set Laravel Horizon's `use` configuration to point to this newly registered connection (e.g., `config('horizon.use', 'dynamic_horizon_connection')`).
    *   Redirect to the standard `/horizon` route, which will now use the dynamically configured Redis connection.

3.  **Middleware (Optional but Recommended)**
    *   A middleware to intercept requests to `/horizon` and apply the dynamic Redis/Horizon configuration based on the stored selection. This ensures Horizon always uses the correct connection.

---

## Next Steps
Begin implementation with Phase 1: Database and Models.