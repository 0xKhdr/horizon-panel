# Horizon Panel — Laravel Dynamic Redis Manager

A lightweight Laravel application to manage multiple Redis connections dynamically, generate Horizon supervisors per connection, and provide an admin UI with Filament.

This repository is targeted at modern technologies: Laravel 12 and Filament 4.

## Project overview
- Manage Redis connection records (host, port, password, tls/options, labels/groups) from a Filament admin UI.
- Dynamically register Redis connections at runtime (no manual config edits required).
- Generate Horizon supervisor configurations per Redis connection and reload Horizon to apply changes.
- Health checks, queue statistics, alerts, and RBAC for admin users.

See `plan.md` for the full development plan, milestones, and migration checklist.

## Requirements
- PHP: >= 8.2 (8.3 recommended)
- Composer: 2.2+
- Node: 18+ (if building frontend assets)
- Database: MySQL / PostgreSQL (any supported by Laravel)
- Redis: used for queues and Horizon (multiple external instances supported)

Optional for local/dev: Docker and docker-compose (see `compose/` directory).

## Getting started — native (local) environment
1. Clone the repository and install dependencies:

```bash
git clone <repo-url> horizon-panel
cd horizon-panel
composer install --no-interaction --prefer-dist
npm install # only if you need to build frontend assets
```

2. Copy the environment file and generate an app key:

```bash
cp .env.example .env
php artisan key:generate
```

3. Configure `.env` values (DB connection, default Redis connection, mail, etc.).

4. Run migrations and seeders (if any):

```bash
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder
```

5. Start the app:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

6. Start Horizon (for local testing):

```bash
php artisan horizon
```

7. Log in to Filament (create a user if needed) and start adding Redis connections from the admin UI.

Notes:
- Dynamic Redis connections are registered at runtime by the `DynamicRedisServiceProvider` (see `app/Providers`).
- When Horizon config is changed by the app, run `php artisan horizon:terminate` (the app may call this automatically after generating config) so Horizon restarts and reads the new supervisor definitions.

## Getting started — Docker (recommended for consistent environment)
There is a `compose/` folder with service definitions. Update any PHP image or Dockerfile there to match PHP >= 8.2.

A simple flow:

```bash
# build/bring up containers (adjust service names if needed)
docker-compose up --build -d
# attach to the app container for commands
docker-compose exec app bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan horizon &
```

Adjust commands based on your compose setup (service names and Dockerfiles live in `compose/`).

## Development workflow & branching
- Branches: `feature/*`, `fix/*`, `hotfix/*`, `upgrade/*`
- Main branch: `main` (protected). Use PRs with review and passing CI.
- PR checklist: tests green, migrations included, composer.lock updated, `CHANGELOG.md` updated under `Unreleased` if the PR introduces user-visible changes.

## Changelog and releases
We use `CHANGELOG.md` (Keep-a-Changelog style). Add release notes under `Unreleased` for PRs that change behavior, add migrations, or introduce user-visible features. When releasing, move `Unreleased` to a versioned section and tag the release.

Example release commands:

```bash
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin --tags
```

## Security notes
- Redis credentials stored in the DB must be encrypted at rest. Use Laravel's encrypted casts (e.g., `$casts = ['password' => 'encrypted']`) or a secrets manager for production.
- Do not log secrets or print credentials to console in production.
- Protect Filament routes with RBAC (Spatie permission package recommended).

## Tests & quality gates
- Run unit and feature tests with:

```bash
php artisan test
```

- Lint and static analysis (if configured): phpstan/psalm, php-cs-fixer, or phpcs.

## Useful artisan commands
- `php artisan horizon` — start Horizon
- `php artisan horizon:terminate` — restart Horizon to pick up config changes
- `php artisan migrate` — run migrations
- `php artisan tinker` — interactive console

## Next steps & pointers
- Follow `plan.md` to execute the Laravel 12 + Filament 4 migration checklist.
- Consider adding a PR template that reminds contributors to update `CHANGELOG.md`.
- Optionally add a CI job to validate changelog entries on PRs that modify source files or migrations.

---

If you'd like, I can now:
- Add a PR template that includes a changelog checklist entry.
- Add a minimal GitHub Actions workflow to run tests and validate changelog usage.

_Last updated: 2025-10-19_

