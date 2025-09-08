# MySQL Migration Guide - Sponsoring24

## Overview
This document outlines the migration from PostgreSQL to MySQL for the Sponsoring24 application. The migration maintains all UUID functionality while ensuring MySQL compatibility.

## Changes Made

### 1. Database Configuration
- **`.env.example`**: Updated default database connection from `pgsql` to `mysql`
- **Port**: Changed from `5432` (PostgreSQL) to `3306` (MySQL)
- **Host**: Updated to `127.0.0.1` for local development

### 2. UUID Handling
- **HasUuid Trait**: Updated for MySQL compatibility
- **Column Type**: All UUID columns now use `CHAR(36)` instead of native UUID type
- **Functionality**: UUID generation and primary key behavior preserved

### 3. Migration Files Updated
All migration files have been updated to use `char(36)` instead of `uuid()`:

#### Core Tables:
- `users` - Primary keys and foreign keys
- `projects` - Primary keys and `created_by` references
- `participants` - Primary keys and relationships
- `donations` - All UUID foreign key relationships
- `supporters` - Primary key conversion

#### Supporting Tables:
- `licenses` - User relationships
- `email_templates` - Project relationships  
- `bonus_credits` - User and referrer relationships
- `member_groups` - User creation tracking
- `personal_access_tokens` - Tokenable ID references

#### Permission System (Spatie):
- `permissions` - Primary keys
- `roles` - Primary keys and team references
- `model_has_permissions` - All relationship columns
- `model_has_roles` - All relationship columns
- `role_has_permissions` - Permission and role references

### 4. New Migration Files
- `2025_01_08_000000_convert_uuid_columns_for_mysql.php` - Base conversion helper
- `2025_01_08_000001_update_all_migrations_for_mysql.php` - Comprehensive UUID updates

## Database Schema Compatibility

### UUID Storage
- **PostgreSQL**: Native `UUID` type (128-bit)
- **MySQL**: `CHAR(36)` storing string representation
- **Format**: `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx`

### JSON Columns
- **PostgreSQL**: `JSONB` (binary JSON)
- **MySQL**: `JSON` (native JSON type)
- **Compatibility**: Laravel handles conversion automatically

### Foreign Key Relationships
All foreign key relationships maintained with proper `CHAR(36)` references:
```sql
$table->char('user_id', 36);
$table->foreign('user_id')->references('id')->on('users');
```

## Deployment Instructions

### For Fresh Installation:
1. Update `.env` with MySQL credentials:
   ```bash
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sponsoring24
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

2. Run migrations:
   ```bash
   php artisan migrate --seed
   ```

### For Existing PostgreSQL Data:
1. Export existing data from PostgreSQL
2. Convert UUID formats if necessary (should be compatible)
3. Import to MySQL database
4. Run migration status check:
   ```bash
   php artisan migrate:status
   ```

## Testing Migration

### 1. Database Connection Test
```bash
php artisan tinker
>>> DB::connection()->getPdo();
>>> DB::select('SELECT 1 as test');
```

### 2. UUID Generation Test
```bash
php artisan tinker
>>> $user = new App\Models\User();
>>> $user->name = 'Test User';
>>> $user->email = 'test@example.com';
>>> $user->password = bcrypt('password');
>>> $user->save();
>>> echo $user->id; // Should show UUID format
```

### 3. Relationship Test
```bash
php artisan tinker
>>> $project = new App\Models\Project();
>>> $project->name = ['en' => 'Test Project'];
>>> $project->created_by = $user->id;
>>> $project->save();
>>> echo $project->id; // Should show UUID
>>> echo $project->created_by; // Should match user ID
```

## Performance Considerations

### Indexing
- `CHAR(36)` columns are properly indexed for foreign key relationships
- Primary key performance maintained with proper indexing
- Consider composite indexes for frequently queried UUID combinations

### Storage
- `CHAR(36)` uses more storage than binary UUID (36 bytes vs 16 bytes)
- Trade-off for MySQL compatibility and human-readable format
- Acceptable for most applications given UUID benefits

## Rollback Strategy

### To PostgreSQL:
1. Checkout previous commit before migration
2. Restore PostgreSQL database backup
3. Update `.env` back to PostgreSQL settings

### Branch Management:
- Current changes in `feature/mysql-migration` branch
- Original PostgreSQL setup preserved in main branch
- Can merge or create separate deployment branches as needed

## Verification Checklist

- [ ] All migration files use `char(36)` instead of `uuid()`
- [ ] HasUuid trait updated for MySQL compatibility
- [ ] Foreign key relationships properly defined
- [ ] Database connection configured for MySQL
- [ ] UUID generation working correctly
- [ ] Model relationships functioning
- [ ] Spatie permission system compatible
- [ ] JSON columns working (projects name/description)
- [ ] All existing functionality preserved

## Notes

- UUID functionality is fully preserved
- All model relationships work identically
- Spatie Laravel Permission package compatibility maintained
- Laravel's Eloquent ORM handles the differences transparently
- No application code changes required beyond database configuration
