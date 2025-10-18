# Laravel Dynamic Redis Horizon Manager - Project Plan (Dev-Ready)

> Purpose: Manage multiple Redis connections dynamically with Laravel + Horizon and a Filament admin UI.

---

## Summary
This document is the single source of truth for planning and running development for the project. It is updated to target Laravel 12 and Filament 4 (latest tech). Use this to: plan work, run upgrades, assign tasks, and validate quality gates.

- Framework: Laravel 12
- Admin UI: Filament 4
- Queue monitor: Laravel Horizon (compatible release)
- PHP: >= 8.2 (8.3 recommended)

---

## Checklist (what this update provides / what we'll enforce)
- [x] Concrete upgrade checklist to Laravel 12 + Filament 4
- [x] Development workflow, branch & PR rules
- [x] Phased milestones with tasks and example commands
- [x] QA/quality gates and acceptance criteria
- [x] Minimal CI / local dev steps

---

## Goals (short)
- Manage Redis connections from DB via Filament admin
- Generate runtime Horizon supervisor configs per connection
- Run and monitor queues across many Redis instances
- Connection health checks, statistics and alerts
- Secure credential storage and RBAC for admin users

---

## Project Conventions
- Branches: feature/*, fix/*, hotfix/*, upgrade/*
- Main branch: `main` (protected). Merge via PR after code review and passing CI.
- PR checklist: tests green, composer.lock updated, migrations included, docs updated, changelog entry.
- Issue and milestone mapping: use GitHub Issues / Milestones per release (v1.0, v1.1...)

---

## Minimal environment requirements
- PHP 8.2+ (8.3 recommended)
- Composer 2.2+
- Node 18+ (if building frontend assets)
- MySQL or PostgreSQL
- Redis instances (for testing: separate local instances / Docker)

Docker images and compose are in `compose/` for local dev — update Dockerfiles to PHP >= 8.2 when upgrading runtime.

---

## High-level Phases & Deliverables (updated for Laravel 12 + Filament 4)

Phase 0 — Prep & Upgrade (small, immediate)
- Update `composer.json` constraints to Laravel 12 and Filament 4
- Ensure PHP runtime updated in Dockerfile / server images
- Create branch: `upgrade/laravel-12-filament-4`
- Deliverable: repo compiles and `php artisan` runs locally

Phase 1 — Core (v1.0) — Redis management + Horizon integration
- RedisConnection model + Filament Resource
- DynamicRedisServiceProvider to register DB-driven Redis connections at runtime
- ConfigGeneratorService to write validated `config/horizon.php` or supply runtime supervisors
- Filament admin: CRUD, test connection action
- Deliverable: Admin can add connections and Horizon monitors their queues (manual restart ok)

Phase 2 — Monitoring & Health (v1.1)
- `CheckRedisHealth` scheduled command and `redis_health_logs` migration
- QueueStats service and Filament widgets
- Notifications for down/failed states
- Deliverable: Health dashboard and alerts

Phase 3 — Security & Teams (v1.2)
- RBAC (Spatie) integration + Filament role assignment UI
- Encrypted credential storage (model cast) and secrets best-practice
- Backup/export/import for connection configs
- Deliverable: Secure multi-role admin and import/export flows

Phase 4 — Polish & Scale (v2.0 planned)
- Performance tuning, caching strategy, supervisor orchestration for large connection sets
- API for external integrations, multi-tenant support

---

## Concrete Upgrade & Migration Checklist (copyable steps)

1. Pre-checks
- Ensure a clean git working tree and create branch:

```bash
git checkout -b upgrade/laravel-12-filament-4
```

- Check current PHP: `php -v` (update Dockerfile / runtime if <8.2)

2. Composer constraints (edit `composer.json`)
- Set PHP constraint: `"php": ">=8.2"`
- Set `laravel/framework` to `"^12.0"`
- Set `filament/filament` to `"^4.0"`
- Check other dependencies (spatie, horizon) for compatibility

3. Update packages

```bash
composer update --with-all-dependencies
composer require filament/filament:"^4.0" --update-with-dependencies
composer require laravel/horizon --update-with-dependencies
```

If composer reports conflicts, run `composer why-not <pkg> <version>` to find blockers and address them one-by-one.

4. Publish vendor configs and follow upgrade guides

```bash
php artisan vendor:publish --tag=filament-config
php artisan vendor:publish --tag=horizon-config
```

Follow the Laravel 12 upgrade guide and Filament 4 docs for breaking changes. Update code to remove deprecated helpers and adjust resource/widget APIs.

5. Code changes to expect
- Filament resources/widgets APIs changed between v3 → v4: update fields, form & table definitions and widget `mount`/`render` lifecycles.
- Update `DynamicRedisServiceProvider` to register connections using `config(['database.redis' => $connections])` or register RedisManager resolvers.
- When writing `config/horizon.php`, ensure the `supervisors` array follows the Horizon version's schema.

6. Restart workers
- After config/horizon.php changes, run:

```bash
php artisan horizon:terminate
# supervisor / systemd will restart horizon process and new config will be read
```

7. Run migrations & tests

```bash
php artisan migrate
php artisan test
```

---

## Implementation details (contract + patterns)

DynamicRedisServiceProvider
- Contract:
  - Input: `RedisConnection` DB records (host, port, password, tls, options, name)
  - Output: entries available under `config('database.redis')` as named connections
  - Error modes: invalid/unauthorized connection — surface via connection test (Artisan command / UI action)

- Implementation notes:
  - Use model casts to encrypt passwords at rest: `$casts = ['password' => 'encrypted'];`
  - Load connections in `register()` and merge into existing config. Avoid relying solely on `config:cache` for dynamic parts.

Horizon config generation
- Use `ConfigGeneratorService` to produce a validated `config/horizon.php` array and write atomically (write to temp file then move).
- After writing, call `php artisan horizon:terminate` so running horizon picks up new supervisors.

Health & Scheduler
- `CheckRedisHealth` command: ping connections, measure latency, store logs in `redis_health_logs` table and emit events for alerts.
- Schedule in `app/Console/Kernel.php` with a configurable interval.

Security
- Store Redis credentials encrypted at rest using Laravel encryption casting.
- Limit Filament admin routes by roles/permissions (Spatie).
- Avoid printing secrets in logs.

---

## Tasks, Example Commands and Files to Create

Immediate dev tasks (small PRs)
- [ ] Add encrypted cast to `RedisConnection` model
  - edit `app/Models/RedisConnection.php` -> `$casts = ['password' => 'encrypted'];`

- [ ] Scaffold health command + migration

```bash
php artisan make:command CheckRedisHealth
php artisan make:model RedisHealthLog -m
```

Files:
- `app/Console/Commands/CheckRedisHealth.php`
- `database/migrations/xxxx_create_redis_health_logs_table.php`
- `app/Models/RedisHealthLog.php`
- `app/Filament/Widgets/RedisHealthWidget.php`

- [ ] Horizon config generator

Files:
- `app/Services/ConfigGeneratorService.php`
- `app/Providers/DynamicRedisServiceProvider.php` (ensure provider registered in `config/app.php`)

- [ ] Filament resources & widgets (update for v4 if migrating)

---

## QA / Quality Gates (run on PRs and before merging to main)
- Build & basic local smoke
  - composer install (no errors)
  - php artisan migrate --env=testing
  - php artisan serve (sanity)

- Tests
  - Unit tests: `php artisan test --testsuite=Unit`
  - Feature tests: `php artisan test --testsuite=Feature`

- Static checks (if configured)
  - php -l for syntax, phpstan/psalm if available

- Acceptance criteria for a release PR:
  - All tests pass
  - No composer conflicts
  - Migrations present and reviewed
  - Filament UI renders and basic CRUD for RedisConnection works
  - Horizon loads generated supervisors after `horizon:terminate`

---

## CI / Pipeline (minimal)
- Steps to run on PRs:
  1. Checkout
  2. composer install --no-interaction --prefer-dist
  3. Setup .env.testing from template
  4. php artisan key:generate
  5. php artisan migrate --env=testing --force
  6. php artisan test

Add caching for composer and node_modules to speed runs.

---

## Project Management Tips
- Make small PRs scoped to a single behavior (e.g., cast + migration, provider + service, Filament resource changes).
- Create an issue for each feature and link PRs to issues.
- Keep upgrade work in `upgrade/*` branches and review package conflicts in isolation.

---

## Timeline (suggested)
- Week 0 (2–3 days): Prep + composer updates + PHP runtime updates
- Week 1 (5 days): Core features + DynamicRedisServiceProvider + Filament CRUD
- Week 2 (5 days): Horizon config generator + basic health command + UI widgets
- Week 3 (5 days): Notifications + RBAC + Export/Import
- Week 4 (buffer + polish): Tests, docs, deploy to staging

Adjust based on team size and availability.

---

## Next actionable choices (pick one and I can implement it)
1. I will scan the repository for current `composer.json` and installed versions and create a precise composer.json diff + commands to run.
2. I will scaffold `CheckRedisHealth` command, migration, and the `RedisHealthLog` model.
3. I will implement a `DynamicRedisServiceProvider` skeleton and `ConfigGeneratorService` to safely write horizon config and trigger reload.
4. I will perform the composer upgrade in an `upgrade/laravel-12-filament-4` branch and attempt to run tests and fix compatibility issues.

Tell me which one to run next and I will execute it.

---

## Change log
- 2025-10-19: Upgraded plan to target Laravel 12 and Filament 4; added concrete upgrade/migration checklist, QA gates, and next actionable tasks.

---

<small>Keep this file as the authoritative development plan and update it when milestones or constraints change.</small>
