# FireCrackers Ecommerce (Laravel)

Complete Laravel storefront + admin panel for selling firecrackers.

## Features

- Admin login with dashboard
- Categories and products CRUD (image, name, price, discount, description, category, stock)
- Public catalog with category filter and search
- Session cart (works for guests)
- Checkout with delivery address
- Payment: Cash on Delivery + Razorpay online
- Guest checkout or optional customer account with order history
- Cart preserved / restored when logging in

## Requirements

- PHP 8.2+
- Composer
- MySQL (recommended) or SQLite
- Node.js (for Vite assets / Breeze)

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### Database (MySQL)

Create a database, then in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=firecrackers
DB_USERNAME=root
DB_PASSWORD=
```

### Database (SQLite quick start)

```env
DB_CONNECTION=sqlite
```

Ensure `database/database.sqlite` exists:

```bash
# Windows PowerShell
New-Item -ItemType File -Path database/database.sqlite -Force
```

### Migrate, seed, storage

```bash
php artisan migrate --seed
php artisan storage:link
npm install && npm run build
php artisan serve
```

Open http://127.0.0.1:8000

## Default admin

Set in `.env` (used by seeder):

```env
ADMIN_EMAIL=admin@firecrackers.test
ADMIN_PASSWORD=password
```

Login at `/login`, then open `/admin`.

## Razorpay (online payments)

```env
RAZORPAY_KEY=rzp_test_xxx
RAZORPAY_SECRET=your_secret
```

Without these keys, checkout still works with **Cash on Delivery**; the Razorpay option stays disabled.

## Main URLs

| URL | Purpose |
|-----|---------|
| `/` | Store home / catalog |
| `/cart` | Cart |
| `/checkout` | Checkout |
| `/login` `/register` | Optional customer account |
| `/account/orders` | Customer order history |
| `/admin` | Admin panel |

## Notes

- Product images are stored under `storage/app/public/products`
- Order prices are snapshotted at purchase time
- Guest order confirmation pages are session-guarded after checkout
