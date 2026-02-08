<h1 align="center">IELTS Band Booster</h1>

<p align="center">
  <strong>A premium, production-ready SaaS application for IELTS preparation.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel 12">
  <img src="https://img.shields.io/badge/TailwindCSS-4.0-06B6D4?style=for-the-badge&logo=tailwindcss" alt="TailwindCSS v4">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js" alt="Alpine.js">
  <img src="https://img.shields.io/badge/Stripe-Integrated-635BFF?style=for-the-badge&logo=stripe" alt="Stripe">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="MIT License">
</p>

<p align="center">
  Built with the <strong>Aceternity Dark Theme</strong> — a stunning, high-contrast design system featuring glassmorphism effects, subtle animations, and a zinc-950/indigo-500 color palette.
</p>

---

## 📖 Table of Contents

- [Introduction](#-introduction)
- [Demo Credentials](#-demo-credentials)
- [Quick Start](#-quick-start)
- [Tech Stack](#-tech-stack)
- [Key Features](#-key-features)
  - [Premium UI/UX](#-premium-uiux)
  - [Authentication System](#-authentication-system)
  - [Billing & Subscriptions](#-billing--subscription-engine-stripe)
  - [Support Ticket System](#-support-ticket-system)
  - [Admin Panel](#-advanced-admin-panel)
  - [API & Developer Experience](#-api--developer-experience)
  - [Notifications System](#-in-app-notification-center)
- [Installation Guide](#-installation-guide)
- [Configuration](#-configuration)
- [Key Routes](#-key-routes)
- [Feature Flags](#-feature-flags)
- [Project Structure](#-project-structure)
- [Testing](#-testing)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [License](#-license)

---

## 📖 Introduction

**IELTS Band Booster** is a high-end SaaS platform designed for IELTS test preparation. It provides a comprehensive suite of tools for learners while offering administrators a powerful, feature-rich backend to manage users, billing, support, and system configuration—all wrapped in a beautiful, modern dark theme UI.

This application is built from the ground up with developer experience and end-user premium feel in mind. It leverages the power of **Laravel 12** for a robust backend, **TailwindCSS v4** for utility-first styling, and **Alpine.js** for lightweight, reactive frontend interactions.

---

## 🔑 Demo Credentials

After running `php artisan migrate --seed`, the following demo accounts are available:

| Role | Email | Password | Username |
|------|-------|----------|----------|
| **Admin** | `admin@demo.com` | `password` | `admin` |
| **User** | `user@demo.com` | `password` | `user` |

> **Note:** These credentials are for development/testing only. Create secure admin accounts for production use.

---

## ⚡ Quick Start

```bash
# Clone & setup
git clone https://github.com/prayangshuuu/IELTSBandBooster.git
cd IELTSBandBooster
composer install && npm install
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --seed

# Run (in separate terminals)
php artisan serve          # → http://127.0.0.1:8000
npm run dev                # → Vite HMR

# Or run everything at once with Composer
composer dev
```

---

## 🛠️ Useful Commands

| Command | Description |
|---------|-------------|
| `php artisan serve` | Start the Laravel development server |
| `npm run dev` | Start Vite development server with HMR |
| `npm run build` | Build production assets |
| `composer dev` | Run server, queue, logs & Vite concurrently |
| `php artisan migrate --seed` | Run migrations and seed demo data |
| `php artisan migrate:fresh --seed` | Reset database and reseed |
| `php artisan queue:listen` | Process queued jobs (emails, webhooks) |
| `php artisan cache:clear` | Clear application cache |
| `php artisan config:clear` | Clear configuration cache |
| `php artisan test` | Run PestPHP test suite |

---

## 🛠️ Tech Stack

| Category | Technology |
|----------|------------|
| **Backend** | Laravel 12, PHP 8.2+ |
| **Frontend** | TailwindCSS v4, Alpine.js 3.x |
| **Authentication** | Laravel Fortify (2FA support), Laravel Sanctum (API), Laravel Socialite (OAuth) |
| **Billing** | Stripe PHP SDK |
| **Database** | MySQL 8.x |
| **Caching/Queues** | Redis (or Database fallback) |
| **Build Tools** | Vite 7.x |
| **Testing** | PestPHP |

---

## ✨ Key Features

### 🎨 Premium UI/UX

| Feature | Description |
|---------|-------------|
| **Aceternity Dark Theme** | A consistent, high-contrast dark theme using a curated `Zinc-950` / `Indigo-500` palette with glassmorphism effects and subtle glow accents. |
| **Bento Grid Dashboard** | A modern, widget-based user dashboard providing an at-a-glance view of key information. |
| **Command Palette (`Cmd+K`)** | A global navigation and action menu implemented with Alpine.js for power-user efficiency. Search pages, actions, and settings instantly. |
| **Dark/Light Theme Toggle** | Users can switch between dark and light themes with persistent preference storage. |
| **Fully Responsive** | Mobile-first layouts that adapt seamlessly across all screen sizes. |
| **Smooth Micro-Animations** | Subtle transitions and hover effects for a polished, interactive experience. |

---

### 🔐 Authentication System

A complete, enterprise-grade authentication system with multiple security layers:

| Feature | Description |
|---------|-------------|
| **Email/Password Login** | Standard secure authentication with email verification. |
| **Two-Factor Authentication (2FA)** | TOTP-based 2FA with recovery codes. Users can view, regenerate, and download backup codes. |
| **Social Login (OAuth)** | **Google**, **Facebook**, and **Twitter/X** authentication via Laravel Socialite. Each provider can be independently enabled/disabled from the admin panel. |
| **Password Confirmation** | Sensitive actions require password re-confirmation for security. |
| **Account Banning** | Administrators can ban/unban users, with banned users automatically prevented from accessing the application. |

#### Social Login Architecture

- **Dynamic Configuration**: OAuth credentials are stored in the database and loaded dynamically via a `DynamicSocialiteProvider`.
- **Admin-Controlled**: Each provider (Google, Facebook, Twitter) can be enabled/disabled independently.
- **Auto-Registration**: New users signing in via OAuth are automatically registered.
- **Account Linking**: Existing users can link their social accounts.

---

### 💳 Billing & Subscription Engine (Stripe)

A complete, end-to-end subscription management system powered by **Stripe**.

| Feature | Description |
|---------|-------------|
| **Complete Subscription Lifecycle** | Handles Subscribe, Cancel, Resume, and Grace Period states gracefully. |
| **Admin Plan Management** | Dynamically create, edit, and delete subscription plans, including setting feature limits (e.g., `ai_generations`). |
| **Coupons & Discounts Module** | Admin-managed coupon codes (Percentage or Fixed Amount) with usage limits, expiration dates, and usage tracking. |
| **Automated Invoicing** | PDF invoice generation and a full history view for users. |
| **Stripe Webhooks** | Automatically syncs subscription state from Stripe events (`checkout.session.completed`, `customer.subscription.updated`, etc.). |
| **Middleware Gating** | Routes are automatically blocked if the subscription is inactive, on a grace period, or if the billing module itself is disabled via feature flags. |
| **Plan Feature Limits** | Enforce per-plan feature limits (e.g., "You can only generate X items per month"). |

#### Webhook Events Handled

| Event | Action |
|-------|--------|
| `checkout.session.completed` | Creates/activates subscription |
| `customer.subscription.updated` | Syncs subscription status changes |
| `customer.subscription.deleted` | Marks subscription as cancelled |
| `invoice.payment_succeeded` | Records successful payment |
| `invoice.payment_failed` | Handles failed payments |

---

### 🎫 Support Ticket System

A full-featured helpdesk system for managing user inquiries.

| Feature | Description |
|---------|-------------|
| **User Workflow** | Users can create new tickets with detailed descriptions and file attachments. |
| **Ticket Statuses** | `Open`, `In Progress`, `Resolved`, `Closed` — with admin-managed transitions. |
| **User Actions** | Users can reply to tickets and close their own tickets. |
| **Admin Dashboard** | Admins can view all tickets, reply to users, and manage ticket status. |
| **Auto-Reply System** | A configurable automatic response sent to users upon ticket creation. Editable in System Settings. |
| **Feature Flag** | The entire support module can be globally toggled on or off from the admin panel. |

---

### ⚙️ Advanced Admin Panel

A comprehensive, secure, and user-friendly admin interface at `/admin`.

| Module | Capabilities |
|--------|--------------|
| **Dashboard** | Overview with key metrics: total users, subscriptions, revenue, recent activity. |
| **System Settings** | GUI for managing App Name, Light/Dark Logos, SMTP Credentials (Host, Port, Username, Password), and Stripe API Keys. |
| **Feature Flags** | Granular toggle switches to enable/disable modules across the application: Billing, Support, User Impersonation, Usernames, Social Login (per-provider). |
| **User Management** | Full CRUD, Bulk Actions (Ban, Delete, Promote, Demote), and CSV Export. |
| **Plan Management** | Create, update, delete subscription plans with custom features and limits. |
| **Coupon Management** | Create and manage discount codes with percentage/fixed discounts, usage limits, and expiration. |
| **Subscription Overview** | View all active subscriptions across all users. |
| **User Impersonation** | Securely log in as any user to debug issues. Gated behind MFA verification and a dedicated feature flag. Impersonated sessions have restricted admin access. |
| **Audit Logs** | Detailed, immutable logs of all significant administrative actions for security and compliance. |
| **API Documentation** | Interactive, "Stripe-like" API reference at `/admin/api/docs`. |

---

### 🧑‍💻 API & Developer Experience

| Feature | Description |
|---------|-------------|
| **REST API (v1)** | A fully-secured RESTful API using Laravel Sanctum for both User and Admin operations. |
| **Interactive API Documentation** | A built-in, "Stripe-like" API Reference page at `/admin/api/docs` with multi-language code examples (cURL, Python, Node.js, PHP). |
| **Personal Access Tokens** | A self-service UI for users to generate and revoke their own API tokens for external integrations. |

---

### 🔔 In-App Notification Center

A real-time notification system for keeping users informed:

| Feature | Description |
|---------|-------------|
| **Bell Icon Widget** | Visible in the navigation bar with unread count badge. |
| **Mark as Read** | Individual and bulk "mark all as read" functionality. |
| **Persistent Storage** | Notifications stored in database for history. |

---

## 🚀 Installation Guide

### Prerequisites

- PHP `>= 8.2`
- Composer `>= 2.x`
- Node.js `>= 20.x` & npm
- MySQL `>= 8.x`
- Stripe Account (for billing features)

### Option A: Docker (Laravel Sail)

Laravel Sail provides a simple Docker-based development environment.

```bash
# 1. Clone the repository
git clone https://github.com/prayangshuuu/IELTSBandBooster.git
cd IELTSBandBooster

# 2. Install Composer dependencies (to get Sail)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs

# 3. Copy environment file and generate app key
cp .env.example .env
./vendor/bin/sail artisan key:generate

# 4. Start the Docker containers
./vendor/bin/sail up -d

# 5. Run migrations and seed the database
./vendor/bin/sail artisan migrate --seed

# 6. Install npm dependencies and build frontend assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

The application will be available at `http://localhost`.

---

### Option B: Manual Installation

```bash
# 1. Clone the repository
git clone https://github.com/prayangshuuu/IELTSBandBooster.git
cd IELTSBandBooster

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure your .env file (see Configuration section below)

# 7. Run database migrations and seed
php artisan migrate --seed

# 8. Start development servers (in separate terminals)
php artisan serve     # Backend: http://127.0.0.1:8000
npm run dev           # Vite dev server
```

---

## ⚙️ Configuration

### Environment Variables (`.env`)

Below are the critical environment variables you need to configure:

#### Application

```env
APP_NAME="IELTS Band Booster"
APP_URL=http://localhost:8000
```

#### Database

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ieltsbandbooster
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### Stripe (Billing)

```env
STRIPE_KEY=pk_live_xxxxxxxxxxxx
STRIPE_SECRET=sk_live_xxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxx
```

#### Mail (SMTP)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="hello@yourapp.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### Redis (Optional but Recommended)

```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### Social Authentication (OAuth)

Social login credentials are managed via the Admin Panel → Settings → Social Authentication. No `.env` configuration required!

---

### Creating the First Admin User

#### Method 1: Database Seeder (Recommended for Development)

The included `DatabaseSeeder` creates a demo admin automatically:

```bash
php artisan db:seed

# Default Admin Credentials:
# Email: admin@demo.com
# Password: password
```

#### Method 2: Laravel Tinker (Production)

For production environments, create an admin via Tinker:

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@yourcompany.com',
    'username' => 'admin',
    'password' => Hash::make('YourSecurePassword'),
    'is_admin' => true,
    'email_verified_at' => now(),
]);
```

---

## 🗺️ Key Routes

### Public Routes

| URL | Description |
|-----|-------------|
| `/` | Public landing page |
| `/auth/{provider}/redirect` | Social login redirect (google, facebook, twitter) |
| `/auth/{provider}/callback` | Social login callback |

### User Routes (Authenticated)

| URL | Description |
|-----|-------------|
| `/dashboard` | User Bento Grid Dashboard |
| `/profile` | User profile settings, 2FA management, API tokens |
| `/billing` | Billing hub (subscription status) |
| `/billing/plans` | View available subscription plans |
| `/billing/invoices` | Invoice history |
| `/billing/checkout/{plan}` | Checkout for a specific plan |
| `/support` | User support ticket center |
| `/support/create` | Create new support ticket |
| `/support/{ticket}` | View/reply to ticket |
| `/two-factor/recovery-codes` | View 2FA recovery codes |

### Admin Routes (`/admin/*`)

| URL | Description |
|-----|-------------|
| `/admin/dashboard` | Admin overview dashboard |
| `/admin/settings` | System Settings (App, SMTP, Features, Stripe, Social Auth) |
| `/admin/users` | User management (CRUD, bulk actions, export) |
| `/admin/users/{user}/edit` | Edit user details |
| `/admin/plans` | Subscription plan management |
| `/admin/coupons` | Coupon/discount code management |
| `/admin/subscriptions` | Global subscription overview |
| `/admin/support` | Admin support ticket queue |
| `/admin/audit` | Audit logs |
| `/admin/api/docs` | Interactive API documentation |
| `/admin/impersonate/start/{user}` | Impersonate user (requires 2FA) |
| `/admin/impersonate/stop` | Stop impersonation |

### API Endpoints (v1)

#### Public

| Endpoint | Description |
|----------|-------------|
| `GET /api/v1/ping` | Health check |
| `GET /api/v1/plans` | List available plans |

#### User (Sanctum Auth Required)

| Endpoint | Description |
|----------|-------------|
| `GET /api/v1/me` | Get current user profile |
| `PUT /api/v1/me` | Update current user profile |
| `GET /api/v1/invoices` | List user invoices |
| `GET /api/v1/invoices/{invoice}` | Get specific invoice |
| `POST /api/v1/subscriptions/checkout` | Create checkout session |
| `POST /api/v1/subscriptions/cancel` | Cancel subscription |
| `POST /api/v1/subscriptions/resume` | Resume subscription |

#### Admin (Sanctum + Admin Required)

| Endpoint | Description |
|----------|-------------|
| `GET /api/v1/admin/users` | List all users |
| `POST /api/v1/admin/users` | Create user |
| `PUT /api/v1/admin/users/{user}` | Update user |
| `DELETE /api/v1/admin/users/{user}` | Delete user |
| `GET /api/v1/admin/audit` | View audit logs |
| `GET /api/v1/admin/settings` | View system settings |
| `PUT /api/v1/admin/settings/{key}` | Update system setting |
| `GET /api/v1/admin/plans` | List plans |
| `POST /api/v1/admin/plans` | Create plan |

#### Webhooks

| Endpoint | Description |
|----------|-------------|
| `POST /api/v1/stripe/webhook` | Stripe webhook handler |

---

## 🎛️ Feature Flags

The application uses a centralized feature flag system stored in the `system_settings` table. Toggle modules via **Admin → Settings → Platform Features & Modules**:

| Flag | Description | Default |
|------|-------------|---------|
| `subscription_module_enabled` | Enable/disable billing & subscription routes | `true` |
| `support_enabled` | Enable/disable support ticket system | `true` |
| `impersonation` | Allow admins to impersonate users | `true` |
| `usernames` | Enable unique usernames for users | `true` |
| `auto_reply_enabled` | Auto-reply to new support tickets | `false` |
| `social_login_enabled` | Master toggle for all social login | `false` |
| `google_login_enabled` | Enable Google OAuth login | `true` |
| `facebook_login_enabled` | Enable Facebook OAuth login | `true` |
| `twitter_login_enabled` | Enable Twitter/X OAuth login | `true` |

### How Feature Flags Work

1. **Database Storage**: All flags are stored in `system_settings` table.
2. **Caching**: Settings are cached for 24 hours via `App\Helpers\Feature`.
3. **Middleware**: `feature:{flag}` middleware blocks routes when disabled.
4. **UI Integration**: Blade views use `Feature::enabled('flag')` for conditional rendering.
5. **Instant Updates**: Cache is cleared automatically when settings are updated.

---

## 📁 Project Structure

```
├── app/
│   ├── Actions/               # Fortify authentication actions
│   ├── Console/               # Artisan commands
│   ├── Helpers/               # Feature flags helper
│   │   └── Feature.php        # Centralized feature flag system
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/         # Admin panel controllers (12 files)
│   │   │   │   ├── AdminDashboardController.php
│   │   │   │   ├── AuditController.php
│   │   │   │   ├── CouponController.php
│   │   │   │   ├── ImpersonationController.php
│   │   │   │   ├── PlanController.php
│   │   │   │   ├── SubscriptionController.php
│   │   │   │   ├── SupportTicketController.php
│   │   │   │   ├── SystemSettingsController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── UserSubscriptionController.php
│   │   │   ├── Api/V1/        # REST API controllers
│   │   │   │   ├── Admin/     # Admin API endpoints
│   │   │   │   ├── InvoiceController.php
│   │   │   │   ├── PlanController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   └── SubscriptionController.php
│   │   │   ├── Auth/          # Authentication controllers
│   │   │   │   └── SocialiteController.php  # OAuth handling
│   │   │   ├── Webhook/       # Stripe webhook handler
│   │   │   ├── BillingController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── InvoiceController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── SubscriptionController.php
│   │   │   ├── SupportTicketController.php
│   │   │   └── TwoFactorRecoveryCodesController.php
│   │   ├── Middleware/        # Custom middleware
│   │   │   ├── AdminMiddleware.php
│   │   │   ├── AdminMfaVerificationMiddleware.php
│   │   │   ├── EnsureFeatureEnabled.php
│   │   │   ├── EnsureNotBanned.php
│   │   │   ├── ImpersonationGuard.php
│   │   │   └── PlanLimitMiddleware.php
│   │   └── Requests/          # Form request validation
│   ├── Mail/                  # Mailable classes
│   ├── Models/                # Eloquent models (10 files)
│   │   ├── AuditLog.php
│   │   ├── Coupon.php
│   │   ├── Invoice.php
│   │   ├── Plan.php
│   │   ├── Setting.php
│   │   ├── Subscription.php
│   │   ├── SupportTicket.php
│   │   ├── SupportTicketMessage.php
│   │   ├── SystemSetting.php
│   │   └── User.php
│   ├── Providers/             # Service providers
│   │   └── DynamicSocialiteProvider.php  # Dynamic OAuth config
│   ├── Services/              # Business logic
│   │   └── StripeService.php
│   └── View/                  # View composers
├── config/                    # App configuration files
├── database/
│   ├── migrations/            # Database migrations (30 files)
│   └── seeders/               # Demo data seeders
├── resources/
│   ├── css/                   # Tailwind CSS source
│   ├── js/                    # Alpine.js & app scripts
│   └── views/                 # Blade templates
│       ├── admin/             # Admin panel views
│       │   ├── api/           # API documentation
│       │   ├── audit/         # Audit logs
│       │   ├── coupons/       # Coupon management
│       │   ├── plans/         # Plan management
│       │   ├── settings/      # System settings
│       │   ├── subscriptions/ # Subscription overview
│       │   ├── support/       # Admin support tickets
│       │   └── users/         # User management
│       ├── auth/              # Authentication views
│       ├── billing/           # Subscription & billing views
│       ├── components/        # Reusable Blade components
│       ├── emails/            # Email templates
│       ├── layouts/           # App layouts (app, guest, admin)
│       ├── profile/           # User profile views
│       └── support/           # User support ticket views
├── routes/
│   ├── api.php                # API routes (v1)
│   ├── auth.php               # Authentication routes
│   └── web.php                # Web routes (319 lines)
└── tests/                     # PestPHP tests
```

---

## 🧪 Testing

Run the test suite using PestPHP:

```bash
# Run all tests
php artisan test

# Or using the composer script
composer test

# Run specific test file
php artisan test tests/Feature/AuthTest.php
```

---

## 🔗 Stripe Webhook Setup

For local development, use the Stripe CLI to forward webhooks:

```bash
# Install Stripe CLI (macOS)
brew install stripe/stripe-cli/stripe

# Login to Stripe
stripe login

# Forward webhooks to your local server
stripe listen --forward-to localhost:8000/api/v1/stripe/webhook
```

Copy the webhook signing secret (`whsec_...`) and add it to your `.env`:

```env
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxx
```

---

## 🔐 Social Authentication Setup

Social authentication is configured entirely through the **Admin Panel** — no `.env` changes required!

### Setup Steps

1. **Enable Social Login**: Navigate to **Admin → Settings** and toggle "Enable Social Login" in the Platform Features section.

2. **Configure Providers**: Expand the Social Login section and configure each provider:

   #### Google
   1. Create OAuth credentials at [Google Cloud Console](https://console.cloud.google.com/apis/credentials)
   2. Add authorized redirect URI: `https://yourdomain.com/auth/google/callback`
   3. Enter Client ID and Client Secret in admin panel

   #### Facebook
   1. Create an app at [Facebook Developers](https://developers.facebook.com/)
   2. Add Facebook Login product
   3. Set Valid OAuth Redirect URI: `https://yourdomain.com/auth/facebook/callback`
   4. Enter App ID and App Secret in admin panel

   #### Twitter/X
   1. Create a project at [Twitter Developer Portal](https://developer.twitter.com/)
   2. Enable OAuth 1.0a and set callback URL: `https://yourdomain.com/auth/twitter/callback`
   3. Enter API Key and API Secret in admin panel

3. **Enable Individual Providers**: Toggle each provider on/off independently.

---

## 🔧 Troubleshooting

### Common Issues

| Problem | Solution |
|---------|----------|
| **Styles not loading** | Run `npm run dev` or `npm run build` |
| **419 Page Expired** | Clear browser cookies or run `php artisan cache:clear` |
| **Feature flag not updating** | Run `php artisan cache:clear` to bust cache |
| **Stripe webhooks failing** | Verify `STRIPE_WEBHOOK_SECRET` matches CLI output |
| **Queue jobs not processing** | Run `php artisan queue:listen` in a separate terminal |
| **500 errors on API** | Check `storage/logs/laravel.log` for details |
| **Social login not working** | Ensure master "Enable Social Login" is toggled on AND provider credentials are configured |
| **Can't stop impersonation** | The stop route is always accessible at `/admin/impersonate/stop` |
| **2FA recovery codes not showing** | Requires recent password confirmation |

### Resetting Everything

```bash
# Nuclear reset (drops all tables, re-migrates, re-seeds)
php artisan migrate:fresh --seed
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. **Fork** the repository
2. **Create** a feature branch: `git checkout -b feature/amazing-feature`
3. **Commit** your changes: `git commit -m 'feat: add amazing feature'`
4. **Push** to your branch: `git push origin feature/amazing-feature`
5. **Open** a Pull Request

### Commit Convention

This project follows [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` New features
- `fix:` Bug fixes
- `docs:` Documentation changes
- `style:` Code style changes (formatting, etc.)
- `refactor:` Code refactoring
- `test:` Adding or updating tests
- `chore:` Maintenance tasks

---

## 📄 License

This project is open-sourced software licensed under the **[MIT License](LICENSE)**.

---

<p align="center">
  <sub>Built with ❤️ using Laravel, TailwindCSS, and Alpine.js</sub>
</p>
