# IELTS Band Booster

A comprehensive Laravel 12 web application for IELTS preparation with advanced user management, authentication, and admin features.

## 🚀 Features

### Authentication & Security
- **Email/Password Authentication** with Laravel Fortify
- **Two-Factor Authentication (2FA)** with TOTP support
- **Social Login** (Google OAuth)
- **Email Verification**
- **Password Reset**
- **Session Management** (Database-backed)

### User Management
- **User Profiles** with customizable themes (light/dark)
- **Role-based Access Control** (Admin/User roles)
- **User Banning/Unbanning** system
- **Bulk User Operations** (ban, unban, delete, promote, demote)
- **CSV Export** of user data

### Admin Features
- **Admin Dashboard** with system overview
- **User Management Panel** (CRUD operations)
- **Audit Logging** system for tracking admin actions
- **User Impersonation** with consent-based access
- **System Settings Management**:
  - Application settings (name, logos)
  - SMTP configuration
  - Feature flags (toggle features on/off)
  - API token management (Sanctum)

### API
- **RESTful API** with Sanctum authentication
- **API Documentation** built-in
- **Personal Access Tokens** for API access

### UI/UX
- **Modern Design** with TailwindCSS v4 and DaisyUI
- **Responsive Layout**
- **Dark/Light Theme Support**
- **Alpine.js** for interactive components

---

## 📋 Requirements

- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Node.js**: 18.x or higher
- **NPM**: Latest version
- **MySQL**: 5.7+ or 8.0+
- **Web Server**: Apache/Nginx (or use Laravel's built-in server for development)

---

## 🛠️ Installation Guide

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd IELTSBandBooster
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node Dependencies

```bash
npm install
```

### Step 4: Environment Configuration

1. **Copy the example environment file:**

```bash
cp .env.example .env
```

2. **Generate application key:**

```bash
php artisan key:generate
```

3. **Configure your `.env` file:**

Open `.env` in your text editor and update the following settings:

#### Database Configuration

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ieltsbandbooster
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

#### Application Settings

```env
APP_NAME="IELTS Band Booster"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

#### Session Configuration

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

#### Mail Configuration (Optional - for email features)

For development, you can use `log` driver:

```env
MAIL_MAILER=log
```

For production, configure SMTP:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@ieltsbandbooster.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### Google OAuth (Optional - for social login)

If you want to enable Google login, add these to your `.env`:

```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

To get Google OAuth credentials:
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable Google+ API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URI: `http://localhost:8000/auth/google/callback`

### Step 5: Create the Database

Create a MySQL database for the application:

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE ieltsbandbooster CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create database user (optional but recommended)
CREATE USER 'ieltsbandbooster'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON ieltsbandbooster.* TO 'ieltsbandbooster'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 6: Run Database Migrations

```bash
php artisan migrate
```

This will create all necessary tables including:
- `users` - User accounts
- `sessions` - Session storage
- `cache` - Cache storage
- `jobs` - Queue jobs
- `personal_access_tokens` - API tokens
- `audit_logs` - Admin action logs
- `settings` - System settings
- And more...

### Step 7: Seed the Database (Demo Users)

The application includes a database seeder that creates demo users for testing:

```bash
php artisan db:seed
```

This will create the following demo accounts:

| Role | Email | Password | Username |
|------|-------|----------|----------|
| **Admin** | admin@demo.com | password | admin |
| **User** | user@demo.com | password | user |

> **Note**: These are demo accounts for testing purposes. In production, you should either:
> - Remove these demo accounts from the seeder
> - Change the passwords immediately after deployment
> - Or skip running the seeder entirely

You can now login with either account to test the application!

### Step 8: Create Storage Link

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public` for file uploads.

### Step 9: Build Frontend Assets

For development:

```bash
npm run dev
```

For production:

```bash
npm run build
```

### Step 10: Start the Development Server

```bash
php artisan serve
```

The application will be available at: **http://127.0.0.1:8000**

---

## 🎯 Quick Start (All-in-One Script)

For a fresh installation, you can run all commands at once:

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database (edit .env first!)
# Then run migrations and seed demo users
php artisan migrate
php artisan db:seed

# Build assets and start server
npm run build
php artisan serve
```

---

## 👤 Creating Your First Admin User

After installation, you have several options to create an admin user:

### Option 1: Use Demo Seeder (Easiest)

If you ran `php artisan db:seed` during installation, you already have a demo admin account:

- **Email**: admin@demo.com
- **Password**: password

Simply login at `/login` with these credentials!

### Option 2: Using Tinker

```bash
php artisan tinker
```

Then run:

```php
$user = new App\Models\User();
$user->name = 'Admin User';
$user->email = 'admin@example.com';
$user->password = Hash::make('password123');
$user->email_verified_at = now();
$user->is_admin = true;
$user->save();
```

Press `Ctrl+C` to exit Tinker.

### Option 2: Register and Manually Promote

1. Register a new account through the web interface at `/register`
2. Access the database and update the user:

```sql
UPDATE users SET is_admin = 1 WHERE email = 'your@email.com';
```

### Option 3: Create a Seeder

Create a file `database/seeders/AdminUserSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);
    }
}
```

Then run:

```bash
php artisan db:seed --class=AdminUserSeeder
```

---

## 🔧 Development Workflow

### Running the Application

**Start all development services at once:**

```bash
composer run dev
```

This will start:
- Laravel development server (port 8000)
- Queue worker
- Log viewer (Pail)
- Vite dev server (for hot module replacement)

**Or run services individually:**

```bash
# Development server
php artisan serve

# Queue worker (for background jobs)
php artisan queue:work

# Watch and compile assets
npm run dev

# View logs in real-time
php artisan pail
```

### Running Tests

```bash
# Run all tests
php artisan test

# Or using Pest directly
./vendor/bin/pest

# Run specific test file
php artisan test --filter=UserTest
```

### Code Quality

```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Check code style without fixing
./vendor/bin/pint --test
```

---

## 📁 Project Structure

```
IELTSBandBooster/
├── app/
│   ├── Actions/          # Custom action classes
│   ├── Console/          # Artisan commands
│   ├── Http/
│   │   ├── Controllers/  # Application controllers
│   │   ├── Middleware/   # Custom middleware
│   │   └── Requests/     # Form request validation
│   ├── Models/           # Eloquent models
│   ├── Providers/        # Service providers
│   └── View/             # View composers
├── config/               # Configuration files
├── database/
│   ├── migrations/       # Database migrations
│   ├── seeders/          # Database seeders
│   └── factories/        # Model factories
├── public/               # Public assets (entry point)
├── resources/
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   └── views/            # Blade templates
├── routes/
│   ├── web.php           # Web routes
│   ├── api.php           # API routes
│   ├── auth.php          # Authentication routes
│   └── console.php       # Console routes
├── storage/              # Application storage
├── tests/                # Automated tests
└── vendor/               # Composer dependencies
```

---

## 🔐 Key Routes

### Public Routes
- `/` - Landing page
- `/login` - Login page
- `/register` - Registration page
- `/forgot-password` - Password reset request
- `/auth/google/redirect` - Google OAuth login

### User Routes (Authenticated)
- `/dashboard` - User dashboard
- `/profile` - User profile management
- `/two-factor/recovery-codes` - 2FA recovery codes

### Admin Routes (Admin Only)
- `/admin/dashboard` - Admin dashboard
- `/admin/users` - User management
- `/admin/audit` - Audit logs
- `/admin/settings` - System settings
- `/admin/api/docs` - API documentation

### API Routes
- `/api/*` - API endpoints (require Sanctum authentication)

---

## 🌐 API Usage

### Creating an API Token

1. Login as an admin user
2. Navigate to `/admin/settings`
3. Go to the "API Tokens" tab
4. Create a new token with desired permissions
5. Copy the token (shown only once!)

### Using the API

Include the token in your requests:

```bash
curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
     -H "Accept: application/json" \
     http://localhost:8000/api/users
```

### API Documentation

Full API documentation is available at `/admin/api/docs` when logged in as an admin.

---

## 🎨 Customization

### Changing Application Name

Update in `.env`:

```env
APP_NAME="Your App Name"
```

### Uploading Custom Logo

1. Login as admin
2. Go to `/admin/settings`
3. Upload logo in the "Application Settings" section

### Configuring Feature Flags

Feature flags can be toggled in the admin settings:
- User Impersonation
- Social Login
- Two-Factor Authentication
- And more...

---

## 🚀 Deployment

### Production Environment Setup

1. **Update `.env` for production:**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

2. **Optimize the application:**

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

3. **Build production assets:**

```bash
npm run build
```

4. **Set proper permissions:**

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

5. **Setup queue worker (for background jobs):**

Use Supervisor or similar process manager:

```bash
php artisan queue:work --daemon
```

6. **Setup scheduled tasks (cron):**

Add to crontab:

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Web Server Configuration

#### Nginx Example

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/IELTSBandBooster/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 🐛 Troubleshooting

### Common Issues

**1. "Base table or view not found" error**

Run migrations:
```bash
php artisan migrate
```

**2. "No application encryption key has been specified"**

Generate a new key:
```bash
php artisan key:generate
```

**3. "Permission denied" errors**

Fix storage permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

**4. Assets not loading**

Build assets:
```bash
npm run build
```

Clear cache:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

**5. Database connection errors**

- Verify MySQL is running
- Check `.env` database credentials
- Ensure database exists
- Test connection: `php artisan tinker` then `DB::connection()->getPdo();`

**6. Session errors**

Clear sessions:
```bash
php artisan session:table
php artisan migrate
```

---

## 📚 Technology Stack

- **Backend**: Laravel 12.x (PHP 8.2+)
- **Frontend**: TailwindCSS v4, DaisyUI, Alpine.js
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Fortify + Sanctum
- **Build Tool**: Vite
- **Testing**: Pest PHP

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License.

---

## 📞 Support

For issues and questions:
- Create an issue in the repository
- Check existing documentation
- Review Laravel documentation: https://laravel.com/docs

---

## 🎓 Learning Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Fortify](https://laravel.com/docs/fortify)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [TailwindCSS](https://tailwindcss.com/docs)
- [Alpine.js](https://alpinejs.dev/)
- [DaisyUI](https://daisyui.com/)

---

**Built with ❤️ using Laravel**
