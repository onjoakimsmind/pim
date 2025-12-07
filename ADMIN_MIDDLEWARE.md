# Admin Middleware

## Overview

The `Admin` middleware provides authentication protection for admin routes. It ensures that only authenticated users can access administrative areas of the application.

## Location

- **Middleware**: `app/Http/Middleware/Admin.php`
- **Alias**: `admin`
- **Registration**: `bootstrap/app.php`

## Behavior

When an unauthenticated user attempts to access a protected route:
- The user is redirected to `/admin/login`
- No error message is displayed
- After login, the user can access the requested route

## Usage

### Protecting Routes

Apply the middleware to routes in your route files:

```php
// Single route
Route::get('/admin/users', [UserController::class, 'index'])
    ->middleware('admin');

// Route group
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/settings', [SettingsController::class, 'index']);
});
```

### Admin Route Structure

The admin routes are defined in `routes/admin.php`:

```php
use App\Http\Middleware\HandleAdminInertiaRequests;

// Guest routes (login page)
Route::middleware(['guest', HandleAdminInertiaRequests::class])
    ->prefix('admin')
    ->group(function () {
        Route::get('/login', [LoginController::class, 'form'])
            ->name('admin.login');
    });

// Protected admin routes
Route::middleware(['admin', HandleAdminInertiaRequests::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');
    });
```

## Admin Inertia Setup

Admin routes use a separate Inertia configuration:

- **Root View**: `admin` (defined in `HandleAdminInertiaRequests`)
- **Vue Pages**: Located in `resources/js/admin/pages/`
- **Entry Point**: `resources/js/admin.ts`

When rendering admin pages with Inertia:

```php
// Renders: resources/js/admin/pages/Dashboard.vue
return Inertia::render('Dashboard');

// Renders: resources/js/admin/pages/Users/Index.vue
return Inertia::render('Users/Index');
```

## Middleware Stack

Admin routes typically use this middleware stack:

1. `web` - Laravel's web middleware group
2. `HandleAdminInertiaRequests` - Admin-specific Inertia handler
3. `admin` - Authentication check and redirect

## Extending the Middleware

You can customize the middleware behavior by editing `app/Http/Middleware/Admin.php`:

### Example: Role-Based Access

```php
public function handle(Request $request, Closure $next): Response
{
    if (! $request->user()) {
        return redirect('/admin/login');
    }

    // Check for admin role
    if (! $request->user()->isAdmin()) {
        abort(403, 'Access denied');
    }

    return $next($request);
}
```

### Example: Custom Redirect

```php
public function handle(Request $request, Closure $next): Response
{
    if (! $request->user()) {
        // Store intended URL for post-login redirect
        session()->put('url.intended', $request->url());
        
        return redirect('/admin/login')
            ->with('message', 'Please log in to continue');
    }

    return $next($request);
}
```

## Testing

The middleware is fully tested in `tests/Feature/AdminMiddlewareTest.php`:

```php
// Test guest redirect
public function test_guest_is_redirected_to_admin_login(): void
{
    $response = $this->get('/admin');
    $response->assertRedirect('/admin/login');
}

// Test authenticated access
public function test_authenticated_user_can_access_admin(): void
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/admin');
    $response->assertStatus(200);
}
```

Run tests with:
```bash
lando artisan test --filter=AdminMiddlewareTest
```

## Key Differences from Standard Auth

| Feature | Standard Auth | Admin Middleware |
|---------|--------------|------------------|
| Redirect URL | `/login` | `/admin/login` |
| Inertia Handler | `HandleInertiaRequests` | `HandleAdminInertiaRequests` |
| Root View | `app` | `admin` |
| Vue Pages Path | `resources/js/Pages/` | `resources/js/admin/pages/` |
| Route Prefix | None | `/admin` |

## Integration with Fortify

The admin login can use Laravel Fortify for authentication, but with custom routes:

```php
// routes/admin.php
Route::post('/admin/login', [LoginController::class, 'store'])
    ->middleware('guest')
    ->name('admin.login.submit');
```

## Best Practices

1. **Always use the middleware**: Don't rely on manual auth checks
2. **Keep admin routes separate**: Use `routes/admin.php` for all admin routes
3. **Use route groups**: Apply middleware to groups rather than individual routes
4. **Include Inertia handler**: Always pair with `HandleAdminInertiaRequests`
5. **Test thoroughly**: Verify both guest and authenticated access

## Troubleshooting

### Infinite Redirect Loop
If you get redirected repeatedly:
- Check that `/admin/login` is NOT protected by the admin middleware
- Ensure `HandleAdminInertiaRequests` is applied to login routes

### 404 on Admin Routes
- Verify the route is defined in `routes/admin.php`
- Check that `routes/admin.php` is required in `routes/web.php`
- Run `lando artisan route:list` to see all registered routes

### Vue Component Not Found
- Ensure Vue component exists in `resources/js/admin/pages/`
- Check the path matches the Inertia render call
- Remember admin pages use a different base path than frontend pages
