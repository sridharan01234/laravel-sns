# Authentication System Documentation

The application now has a complete authentication system with the following features:

## Components

1. **Authentication Controllers**
   - `RegisteredUserController` - Handles user registration
   - `AuthenticatedSessionController` - Handles login/logout
   
2. **Middleware**
   - `Authenticate` - Protects routes requiring authentication
   - `RedirectIfAuthenticated` - Prevents authenticated users from accessing guest-only routes

3. **Views**
   - `auth/login.blade.php` - Login form
   - `auth/register.blade.php` - Registration form
   - Uses `AppLayout` component for consistent styling

4. **Routes**
   - All application routes are protected with `auth` middleware
   - Guest routes (login/register) are protected with `guest` middleware
   - Home page redirects to login for unauthenticated users

## Features

- User registration with name, email, and password
- User authentication with email and password
- Remember me functionality
- Protected routes requiring authentication
- Automatic redirection to login page
- Session management and security
- CSRF protection

## Usage

1. Users must register or login to access the application
2. All routes except login/register require authentication
3. Authenticated users are redirected to dashboard
4. Guest users are redirected to login page