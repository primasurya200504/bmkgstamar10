# BMKG STAMAR Project Production Preparation TODO

## Current Status

-   [x] Analyzed project structure, controllers, configs, and assets.
-   [x] Fixed autoload issue via dump-autoload.
-   [x] Generated application key.

## Pending Steps for Production Readiness

1. **Restore Full Dependencies**  
   Run `composer install` (without --no-dev) to ensure all dependencies, including dev ones needed locally, are installed. This fixes any missing packages like deep-copy.

2. **Run Database Migrations**  
   Execute `php artisan migrate --force` to apply all pending migrations (applications, payments, guidelines, etc.). This sets up the schema.

3. **Run Database Seeder**  
   Execute `php artisan db:seed --class=CompleteSeeder` to populate initial data (users, guidelines, etc.). Assumes DB credentials are set in .env.

4. **Clear and Cache Configurations**

    - `php artisan config:clear && php artisan config:cache`
    - `php artisan route:cache`
    - `php artisan view:cache`  
      This optimizes for production by caching configs, routes, and views.

5. **Optimize Autoloader and Classes**  
   Run `php artisan optimize` to generate optimized classmap and autoloader for faster loading.

6. **Verify Assets**  
   Confirm `npm run build` has run (already done); assets are in public/build/.

7. **Test Local Server**  
   Run `php artisan serve` and verify admin/user dashboards load without errors via browser.

8. **Hosting Preparation**
    - Set APP_ENV=production in .env on server.
    - Configure DB credentials (DB_HOST, DB_DATABASE, etc.).
    - Run above steps on server.
    - Set permissions: `chmod -R 775 storage bootstrap/cache` (on Linux server).
    - Ensure web server (Apache/Nginx) points to public/.
    - If queues needed (none apparent), set up supervisor for `php artisan queue:work`.

## Completed Steps

-   [x] Step 1: Restore dependencies
-   [x] Step 2: Migrations (already run)
-   [x] Step 3: Seeding
-   [x] Step 4: Caching
-   [x] Step 5: Optimization
-   [x] Step 6: Assets verification
-   [ ] Step 7: Local testing
-   [ ] Step 8: Hosting checklist provided

_Note: If any step fails (e.g., migration conflicts from duplicate columns like add_date_columns), will investigate and fix specific files._
