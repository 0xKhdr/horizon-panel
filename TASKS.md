# Horizon Panel - Development Tasks

> **Last Updated**: 2025-11-20  
> **Status**: Phases 1-2 Complete, Phase 3 In Progress

---

## Phase 1: Database and Models ✅

- [x] Create applications table migration
- [x] Create redis_connections table migration
- [x] Create application_redis_connection pivot table migration
- [x] Create Application model with relationships
- [x] Create RedisConnection model with relationships and encryption

---

## Phase 2: Admin Panel for Management (CRUD) ✅

- [x] Create ApplicationResource with Filament
- [x] Create RedisConnectionResource with Filament
- [x] Implement form schemas for both resources
- [x] Implement table schemas for both resources
- [x] Set up relationship management (attach/detach)

---

## Phase 3: Dynamic Horizon UI

### Configuration
- [ ] Publish Horizon configuration file
  ```bash
  php artisan vendor:publish --tag=horizon-config
  php artisan vendor:publish --tag=horizon-assets
  ```

### Core Implementation
- [ ] Create HorizonDashboard Filament page
  - [ ] Implement application dropdown selector
  - [ ] Implement dynamic Redis connection dropdown (filtered by application)
  - [ ] Add "Launch Horizon" button
  - [ ] Store selected connection in session

- [ ] Create DynamicHorizonMiddleware
  - [ ] Retrieve selected connection from session
  - [ ] Dynamically configure Redis connection
  - [ ] Set Horizon to use dynamic connection
  - [ ] Handle missing connection gracefully

- [ ] Update routes
  - [ ] Add middleware to Horizon routes
  - [ ] Configure Horizon path

### Testing
- [ ] Test application selection
- [ ] Test connection selection
- [ ] Test Horizon launch with different connections
- [ ] Verify session persistence

---

## Phase 4: Security & Authentication

### User Management
- [ ] Create roles and permissions seeder
  - [ ] Admin role (full access)
  - [ ] Viewer role (read-only)
  - [ ] Define permissions

- [ ] Update User model
  - [ ] Add HasRoles trait
  - [ ] Add activity logging
  - [ ] Add helper methods

### Authorization
- [ ] Configure Filament authentication
  - [ ] Set up auth middleware
  - [ ] Configure auth guard

- [ ] Create HorizonServiceProvider
  - [ ] Implement Horizon authorization gate
  - [ ] Integrate with Spatie Permission
  - [ ] Add activity logging for Horizon access

- [ ] Add policies for resources
  - [ ] ApplicationPolicy
  - [ ] RedisConnectionPolicy

### Security Hardening
- [ ] Verify password encryption for Redis connections
- [ ] Add CSRF protection verification
- [ ] Configure session security settings
- [ ] Set up rate limiting

---

## Phase 5: Testing & Quality Assurance

### Feature Tests
- [ ] ApplicationTest
  - [ ] Test creating applications
  - [ ] Test updating applications
  - [ ] Test deleting applications
  - [ ] Test attaching/detaching connections

- [ ] RedisConnectionTest
  - [ ] Test creating connections
  - [ ] Test password encryption
  - [ ] Test connection validation
  - [ ] Test actual Redis connectivity

- [ ] HorizonDashboardTest
  - [ ] Test application selection
  - [ ] Test connection selection
  - [ ] Test session storage
  - [ ] Test middleware functionality

### Unit Tests
- [ ] ApplicationModelTest
  - [ ] Test relationships
  - [ ] Test mass assignment
  - [ ] Test factory creation

- [ ] RedisConnectionModelTest
  - [ ] Test password encryption/decryption
  - [ ] Test relationships
  - [ ] Test validation rules

### Integration Tests
- [ ] Test full workflow (create app → add connection → launch Horizon)
- [ ] Test with multiple concurrent users
- [ ] Test connection switching

### Code Quality
- [ ] Run Laravel Pint for code style
- [ ] Fix any linting issues
- [ ] Run static analysis (optional: PHPStan)

---

## Phase 6: Production Readiness

### Environment Configuration
- [ ] Create .env.production.example
- [ ] Document all environment variables
- [ ] Set up production database configuration

### Docker Configuration
- [ ] Add Horizon worker to supervisord.conf
- [ ] Test Horizon worker in Docker container
- [ ] Verify worker auto-restart on failure
- [ ] Configure worker memory limits

### Database Optimization
- [ ] Add indexes to applications table
- [ ] Add indexes to redis_connections table
- [ ] Add indexes to pivot table
- [ ] Test query performance with large datasets

### Monitoring & Logging
- [ ] Create health check endpoints
  - [ ] Database health check
  - [ ] Redis health check
  - [ ] Horizon status check

- [ ] Configure error logging
  - [ ] Set up log channels
  - [ ] Configure log levels
  - [ ] Set up log rotation

- [ ] Set up activity logging
  - [ ] Log application CRUD operations
  - [ ] Log connection CRUD operations
  - [ ] Log Horizon access

### Performance
- [ ] Configure Redis connection pooling
- [ ] Optimize Horizon worker configuration
- [ ] Set up queue priorities
- [ ] Configure job timeouts

### Backup & Recovery
- [ ] Document backup procedures for MySQL
- [ ] Test database restore
- [ ] Document Redis backup (if needed)

---

## Phase 7: Documentation & Deployment

### User Documentation
- [ ] Update README.md
  - [ ] Project description
  - [ ] Features list
  - [ ] Installation instructions
  - [ ] Usage guide
  - [ ] Screenshots

- [ ] Create DEPLOYMENT.md
  - [ ] Server requirements
  - [ ] Docker Compose setup
  - [ ] Environment configuration
  - [ ] Database migrations
  - [ ] SSL/TLS setup
  - [ ] Backup procedures

- [ ] Create TROUBLESHOOTING.md
  - [ ] Common issues
  - [ ] Redis connection problems
  - [ ] Horizon worker issues
  - [ ] Permission errors
  - [ ] Database migration errors

### Developer Documentation
- [ ] Document architecture decisions
- [ ] Document API endpoints (if any)
- [ ] Document testing procedures
- [ ] Create contribution guidelines

### Deployment
- [ ] Test full deployment on staging
- [ ] Run all migrations
- [ ] Seed initial data (roles/permissions)
- [ ] Create first admin user
- [ ] Test all functionality
- [ ] Monitor logs for errors
- [ ] Performance testing under load

### Final Checks
- [ ] Security audit
- [ ] Code review
- [ ] Update CHANGELOG.md
- [ ] Tag release version
- [ ] Deploy to production

---

## Quick Commands Reference

### Development
```bash
# Start Docker containers
docker-compose up -d --build

# Run migrations
docker-compose exec app php artisan migrate

# Create admin user
docker-compose exec app php artisan make:filament-user

# Run tests
docker-compose exec app php artisan test

# Code style check
docker-compose exec app ./vendor/bin/pint

# View logs
docker-compose logs -f app
```

### Horizon
```bash
# Publish Horizon assets
docker-compose exec app php artisan horizon:install

# Check Horizon status
docker-compose exec app php artisan horizon:status

# Terminate Horizon
docker-compose exec app php artisan horizon:terminate

# Clear failed jobs
docker-compose exec app php artisan horizon:clear
```

### Database
```bash
# Fresh migration with seed
docker-compose exec app php artisan migrate:fresh --seed

# Rollback migration
docker-compose exec app php artisan migrate:rollback

# Database backup
docker-compose exec mysql mysqldump -u root -p horizon_panel > backup.sql
```

---

## Notes

- Keep this file updated as tasks are completed
- Mark tasks with `[x]` when done
- Add new tasks as they are discovered
- Reference IMPLEMENTATION_PLAN.md for detailed specifications
