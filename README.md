<p align="center">
  <img src="public/logo-dark.svg" alt="IELTS Band Booster Logo" width="200">
</p>

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

## 📖 Introduction

**IELTS Band Booster** is a high-end SaaS platform designed for IELTS test preparation. It provides a comprehensive suite of tools for learners while offering administrators a powerful, feature-rich backend to manage users, billing, support, and system configuration—all wrapped in a beautiful, modern dark theme UI.

This application is built from the ground up with developer experience and end-user premium feel in mind. It leverages the power of **Laravel 12** for a robust backend, **TailwindCSS v4** for utility-first styling, and **Alpine.js** for lightweight, reactive frontend interactions.

---

## ✨ Key Features

### 🎨 Premium UI/UX

| Feature | Description |
|---------|-------------|
| **Aceternity Dark Theme** | A consistent, high-contrast dark theme using a curated `Zinc-950` / `Indigo-500` palette with glassmorphism effects and subtle glow accents. |
| **Bento Grid Dashboard** | A modern, widget-based user dashboard providing an at-a-glance view of key information. |
| **Command Palette (`Cmd+K`)** | A global navigation and action menu implemented with Alpine.js for power-user efficiency. Search pages, actions, and settings instantly. |
| **Fully Responsive** | Mobile-first layouts that adapt seamlessly across all screen sizes. |
| **Smooth Micro-Animations** | Subtle transitions and hover effects for a polished, interactive experience. |

---

### 💳 Billing & Subscription Engine (Stripe)

A complete, end-to-end subscription management system powered by **Stripe**.

| Feature | Description |
|---------|-------------|
| **Complete Subscription Lifecycle** | Handles Subscribe, Cancel, Resume, and Grace Period states gracefully. |
| **Admin Plan Management** | Dynamically create, edit, and delete subscription plans, including setting feature limits (e.g., `ai_generations`). |
| **Coupons & Discounts Module** | Admin-managed coupon codes (Percentage or Fixed Amount) with usage limits and tracking. |
| **Automated Invoicing** | PDF invoice generation and a full history view for users. |
| **Stripe Webhooks** | Automatically syncs subscription state from Stripe events (`checkout.session.completed`, `customer.subscription.updated`, etc.). |
| **Middleware Gating** | Routes are automatically blocked if the subscription is inactive, on a grace period, or if the billing module itself is disabled via feature flags. |

---

### 🎫 Support Ticket System

A full-featured helpdesk system for managing user inquiries.

| Feature | Description |
|---------|-------------|
| **User Workflow** | Users can create new tickets with detailed descriptions and file attachments. |
| **Admin Dashboard** | Admins can view all tickets, reply to users, and manage ticket status (`Open`, `In Progress`, `Resolved`, `Closed`). |
| **Auto-Reply System** | A configurable automatic response sent to users upon ticket creation. Editable in System Settings. |
| **Feature Flag** | The entire support module can be globally toggled on or off from the admin panel. |

---

### ⚙️ Advanced Admin Panel

A comprehensive, secure, and user-friendly admin interface.

| Module | Capabilities |
|--------|--------------|
| **System Settings** | GUI for managing App Name, Light/Dark Logos, SMTP Credentials (Host, Port, Username, Password), and Stripe API Keys. |
| **Feature Flags** | Granular toggle switches to enable/disable modules across the application: Billing, Support, User Impersonation, Usernames. |
| **User Management** | Full CRUD, Bulk Actions (Ban, Delete, Promote, Demote), and CSV Export. |
| **User Impersonation** | Securely log in as any user to debug issues. Gated behind MFA verification and a dedicated feature flag. |
| **Audit Logs** | Detailed, immutable logs of all significant administrative actions for security and compliance. |

---

### 🧑‍💻 API & Developer Experience

| Feature | Description |
|---------|-------------|
| **REST API (v1)** | A fully-secured RESTful API using Laravel Sanctum for both User and Admin operations. |
| **Interactive API Documentation** | A built-in, "Stripe-like" API Reference page at `/admin/api/docs` with multi-language code examples (cURL, Python, Node.js, PHP). |
| **Personal Access Tokens** | A self-service UI for users to generate and revoke their own API tokens for external integrations. |

---

## 🛠️ Tech Stack

| Category | Technology |
|----------|------------|
| **Backend** | Laravel 12, PHP 8.2+ |
| **Frontend** | TailwindCSS v4, Alpine.js 3.x |
| **Authentication** | Laravel Fortify (2FA support), Laravel Sanctum (API) |
| **Billing** | Stripe PHP SDK |
| **Database** | MySQL 8.x |
| **Caching/Queues** | Redis (or Database fallback) |
| **Build Tools** | Vite 7.x |
| **Testing** | PestPHP |

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
git clone https://github.com/your-username/ieltsbandbooster.git
cd ieltsbandbooster

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
git clone https://github.com/your-username/ieltsbandbooster.git
cd ieltsbandbooster

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

---

### Creating the First Admin User

There are two methods to create an admin user:

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

### User Routes

| URL | Description |
|-----|-------------|
| `/` | Public landing page |
| `/dashboard` | User Bento Grid Dashboard |
| `/profile` | User profile settings |
| `/billing` | Billing hub (subscription status) |
| `/billing/plans` | View available subscription plans |
| `/billing/invoices` | Invoice history |
| `/support` | User support ticket center |

### Admin Routes

| URL | Description |
|-----|-------------|
| `/admin/dashboard` | Admin overview dashboard |
| `/admin/settings` | System Settings (App, SMTP, Features, Stripe) |
| `/admin/users` | User management (CRUD, bulk actions) |
| `/admin/plans` | Subscription plan management |
| `/admin/coupons` | Coupon/discount code management |
| `/admin/subscriptions` | Global subscription overview |
| `/admin/support` | Admin support ticket queue |
| `/admin/audit` | Audit logs |
| `/admin/api/docs` | Interactive API documentation |

### API Endpoints (v1)

| Endpoint | Auth | Description |
|----------|------|-------------|
| `GET /api/v1/ping` | Public | Health check |
| `GET /api/v1/plans` | Public | List available plans |
| `GET /api/v1/me` | Sanctum | Get current user profile |
| `PUT /api/v1/me` | Sanctum | Update current user profile |
| `GET /api/v1/invoices` | Sanctum | List user invoices |
| `POST /api/v1/subscriptions/checkout` | Sanctum | Create checkout session |
| `POST /api/v1/subscriptions/cancel` | Sanctum | Cancel subscription |
| `GET /api/v1/admin/users` | Admin | List all users |
| `GET /api/v1/admin/audit` | Admin | View audit logs |
| `GET /api/v1/admin/settings` | Admin | View system settings |

---

## 🧪 Testing

Run the test suite using PestPHP:

```bash
# Run all tests
php artisan test

# Or using the composer script
composer test
```

---

## 📄 License

This project is open-sourced software licensed under the **[MIT License](LICENSE)**.

---

<p align="center">
  <sub>Built with ❤️ using Laravel, TailwindCSS, and Alpine.js</sub>
</p>
