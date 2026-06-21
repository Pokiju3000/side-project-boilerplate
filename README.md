# DeliveryOps

Laravel demo for internal operations teams that need to coordinate client projects from intake to planning, validation, budget tracking, imports and diagnostics.

This repository is designed as a portfolio-safe project: the domain, data, client names and workflows are fictional, while the implementation demonstrates reusable patterns for complex business applications.

## What It Demonstrates

- Microsoft Azure AD authentication through Socialite.
- Optional local demo login for portfolio review.
- Group-based access hooks for regular and admin users.
- Client project dashboard with operational indicators.
- Service lines, delivery templates and project ownership.
- Delivery planning matrix by activity and week.
- Validation center for blocked projects, budget gaps and planning mismatches.
- Project budget summary with revenue, costs, margin and budget lines.
- External import monitor with sync freshness, record counts and warnings.
- Activity history and support resources.
- Admin diagnostics for PHP, database, cache, queue worker and scheduler.
- Queue heartbeat and scheduled maintenance task.

## Local Setup

```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
cmd /c npm install
cmd /c npm run build
php artisan serve
```

Then open `http://127.0.0.1:8000`.

Demo access is enabled in `.env.example`:

- Email: `demo@example.test`
- Local demo button: visible when `DEMO_ACCESS_ENABLED=true`

The demo login creates/authenticates a local user and the seeder provides fictional portfolio data.

## Main Screens

- `/dashboard` - control room with priority projects, validation alerts and recent activity.
- `/projects` - project list grouped around status, plan and budget readiness.
- `/projects/{id}` - project detail with delivery matrix, budget and audit trail.
- `/validation` - quality gate view inspired by real operational review workflows.
- `/imports` - external sync monitor for CRM, ERP and timesheet-style feeds.
- `/resources` - support resources and activity history.
- `/admin/diagnostic` - operational health checks for services to verify first during an incident.

## Privacy

This project does not include private institutional code, real data, internal endpoints, secrets or organization-specific naming. It keeps the reusable application architecture and process ideas while moving the domain to fictional client delivery operations.

## Stack

- Laravel 13
- Blade
- Tailwind CSS
- Alpine.js
- Laravel Socialite
- Microsoft Azure Socialite provider
- Database queue driver
