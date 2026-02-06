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

### Subscription & Billing
- **Stripe Integration** for secure payments
- **Dynamic Plan Management** (Admin controlled)
- **User Subscription Flow** (Checkout, Cancel, Resume)
- **Automated Invoicing** & PDF downloads
- **System Toggles** for enabling/disabling billing
- **Feature & Rate Limiting** middleware enforcement


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

### UI/UX & Architecture
- **Modern Design System**: Built with **TailwindCSS v4** and **Aceternity UI**, featuring a robust Dark/Light theming system.
- **Headless UI Patterns**: Fully accessible interactive components (Dropdowns, Modals, Mobile Menus) implemented with **Alpine.js**.
- **ORM-Driven Logic**: Data access decoupled from views using Laravel Eloquent Accessors for cleaner, maintainable code.
- **Responsive Layout**: Mobile-first design ensuring a consistent experience across all devices.

### Subscription & Billing System
The application features a complete subscription module powered by **Laravel Cashier (Stripe)**.

*   **Stripe Integration**: Secure payment processing for unified reliable billing.
*   **Dynamic Plan Management**: Admins can create, update, and delete plans directly from the dashboard. Plans support custom features and limits (e.g., `max_projects`, `api_calls_per_minute`).
*   **User Subscription Flow**:
    *   **Subscribe**: Users can view plans and subscribe via a hosted Stripe Checkout session.
    *   **Manage**: Users can cancel or resume subscriptions at any time.
    *   **Grace Periods**: Canceled subscriptions remain active until the billing cycle ends.
*   **Automated Invoicing**: System automatically generates invoices. Users can view history and download PDFs.
*   **System Control**:
    *   **Global Toggle**: The entire subscription module can be enabled/disabled via Admin Settings.
    *   **Feature Gating**: Middleware (`EnsureSubscriptionActive`) automatically restricts access to premium routes based on subscription status.



---

## 📋 Requirements

- **Docker Desktop**: Required for running the application via Sail
- **Git**: For version control
- **Composer** (Optional): If running locally without Docker
- **Node.js & NPM** (Optional): If running locally without Docker

---

## 🛠️ Installation Guide

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd IELTSBandBooster
```

### Step 2: Start Application (Docker Method - Recommended)

We recommend using **Laravel Sail** (Docker) for a consistent development environment.

1. **Start the containers** (Database, Redis, Mailpit, App):
```bash
./vendor/bin/sail up -d
```

2. **Install Composer Dependencies**:
```bash
./vendor/bin/sail composer install
```

3. **Install Node Dependencies & Build Assets**:
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

4. **Setup Environment**:
```bash
cp .env.example .env
./vendor/bin/sail artisan key:generate
```

5. **Configure Database**:
Open `.env` and set the following (configured for Docker internal network):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ieltsbandbooster
DB_USERNAME=ieltsbandbooster
DB_PASSWORD=password
```
*Note: We use `127.0.0.1` so you can also access the DB from your local machine tools.*

6. **Run Migrations & Seed**:
```bash
./vendor/bin/sail artisan migrate --seed
```

### Step 2: Start Application (Manual Method)

If you prefer running PHP and MySQL directly on your machine:

1. Install dependencies:
```bash
composer install
npm install
npm run build
```

2. Configure `.env` with your local database credentials.

3. Run migrations:
```bash
php artisan migrate --seed
```

4. Start local server:
```bash
php artisan serve
```
The application will be available at **http://127.0.0.1:8000**.

---

## 🎯 Quick Start (All-in-One Script)

For a fresh installation using Sail (Docker):

```bash
# Start Docker containers
./vendor/bin/sail up -d

# Install dependencies
./vendor/bin/sail composer install
./vendor/bin/sail npm install

# Setup environment
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed

# Build assets
./vendor/bin/sail npm run build
```

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

If you ran `php artisan db:seed` during installation, the following accounts are available:

| Role | Email | Password | Username |
|------|-------|----------|----------|
| **Demo Admin** | `admin@demo.com` | `password` | `admin` |
| **Demo User** | `user@demo.com` | `password` | `user` |
| **Personal Admin** | `prayangshuuu@gmail.com` | `Test@321` | `prayangshuuu` |
| **Personal User** | `prayangshu073@gmail.com` | `Test@321` | `prayangshu` |

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

### Billing Routes
- `/billing/checkout/{plan}` - Initiate subscription
- `/billing/invoices` - View invoice history
- `/billing/invoices/{invoice}` - Download invoice PDF

### Admin Routes (Admin Only)
- `/admin/dashboard` - Admin dashboard
- `/admin/users` - User management
- `/admin/plans` - Subscription plan management
- `/admin/audit` - Audit logs
- `/admin/settings` - System settings
- `/admin/api/docs` - API documentation

### API Routes (v1)
- `/api/v1/plans` - List active plans
- `/api/v1/subscriptions/checkout` - Create checkout session
- `/api/v1/subscriptions/cancel` - Cancel subscription
- `/api/v1/invoices` - List user invoices
- `/api/v1/*` - Other authenticated endpoints

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
* * * * * cd /path-to-your-project && ./vendor/bin/sail artisan schedule:run >> /dev/null 2>&1
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

- **Sail Users**: Check if Docker is running. Ensure `.env` has `DB_HOST=127.0.0.1` (we use port forwarding).
- **Manual**: Verify local MySQL service is running.

**6. Database "Access Denied"**

If you see access denied errors with Sail:
```bash
./vendor/bin/sail down -v
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
```
This resets the database volume to match your `.env` password.

---

## 📚 Technology Stack

- **Backend**: Laravel 12.x (PHP 8.2+)
- **Frontend**: TailwindCSS v4, Aceternity UI, Alpine.js
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
- [Aceternity UI](https://ui.aceternity.com/)

---

**Built with ❤️ using Laravel**
