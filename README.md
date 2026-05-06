# Final State E-commerce

Backend/API application built with Laravel 12 and a Vite/Tailwind frontend toolchain.

## Features

- JWT-based authentication for users and offices
- Property listing and office management endpoints
- Favorites, ratings, and office follow functionality
- Admin approval and moderation endpoints
- Visitor endpoints for browsing recent offers and properties

## Tech Stack

- PHP 8.2+ / Laravel 12
- MySQL/SQL database via Laravel migrations
- JWT Auth (`tymon/jwt-auth`)
- Sanctum (`laravel/sanctum`)
- Vite + Tailwind CSS 4 + Axios

## Project Structure

```text
app/            Application code (controllers, models, middleware)
api/            Serverless entrypoint (Vercel)
config/         Laravel configuration
database/       Migrations, factories, seeders
public/         Public web root
resources/      Frontend assets/views
routes/         API and web routes
tests/          Feature and unit tests
```

## Prerequisites

- PHP 8.2 or newer
- Composer
- Node.js + npm
- A configured database

## Installation

```bash
git clone https://github.com/eliasnadder/final-state-ecommerce.git
cd final-state-ecommerce

composer install
npm install

cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Running Locally

Run full development stack (Laravel server, queue worker, log viewer, Vite):

```bash
composer dev
```

Or run frontend only:

```bash
npm run dev
```

## Build

```bash
npm run build
```

## Test

```bash
composer test
```

## API Routes

Main API routes are defined in `/routes/api.php`, including grouped routes for:

- `/user/*`
- `/office/*`
- `/visitor/*`
- `/admin/*`

## Deployment Notes

- `api/index.php` provides the Vercel serverless entrypoint.
- `vercel.json` is included for deployment configuration.

## License

MIT
