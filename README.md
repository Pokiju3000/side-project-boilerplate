# Side Project Boilerplate

Starter Laravel pour petits outils internes avec un visuel poli, une connexion Azure AD, une page de diagnostic, une queue et un scheduler dÃ©jÃ  cÃ¢blÃ©s.

L'objectif est de partir vite sans garder de logique mÃ©tier spÃ©cifique au projet source.

## Pourquoi

Ce boilerplate sert Ã  dÃ©marrer rapidement de petits outils internes Laravel dans un contexte organisationnel: authentification Microsoft, contrÃ´le d'accÃ¨s par groupes Azure, queue, scheduler, diagnostic applicatif et base visuelle cohÃ©rente.

Il est conÃ§u pour dÃ©montrer une structure rÃ©utilisable, sÃ©curitaire et maintenable, sans inclure de logique mÃ©tier ni de donnÃ©es provenant d'un projet rÃ©el.


## SÃ©curitÃ© et confidentialitÃ©

Ce dÃ©pÃ´t ne contient aucun code propriÃ©taire, aucune donnÃ©e rÃ©elle, aucun nom d'organisation interne, aucun secret et aucun endpoint privÃ©.

Les variables sensibles doivent Ãªtre configurÃ©es dans `.env` et ne doivent jamais Ãªtre commitÃ©es.



## Ce qui est volontairement gÃ©nÃ©rique

- Aucune logique mÃ©tier spÃ©cifique.
- Aucun modÃ¨le de donnÃ©es propre Ã  une organisation.
- Aucun appel vers des systÃ¨mes internes.
- Aucun exemple avec donnÃ©es rÃ©elles.
- Authentification Azure configurable par variables d'environnement.


## Inclus

- Laravel 13, Blade, Tailwind CSS et Alpine.
- Connexion Microsoft Azure AD via Socialite.
- Mode demo optionnel pour simuler une session sans Azure en local.
- AccÃ¨s protÃ©gÃ© par groupes Azure:
  - `AZURE_ADMIN_GROUP_ID` pour les pages admin.
  - `AZURE_USER_GROUP_ID` pour les utilisateurs rÃ©guliers.
- Dashboard minimal.
- Page admin `/admin/diagnostic`.
- Queue database et heartbeat du worker.
- Scheduler Laravel avec une tÃ¢che d'entretien de base.
- Scripts Composer pour setup, dev, test et final.

## PrÃ©requis

- PHP 8.3 ou plus rÃ©cent.
- Composer.
- Node.js et npm.
- Une base de donnÃ©es MySQL, MariaDB, PostgreSQL, SQL Server ou SQLite.
- Une application Azure enregistrÃ©e si l'authentification Microsoft est utilisÃ©e.



## Commandes pour installation locale

```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
cmd /c npm install
cmd /c npm run build
```

Pour lancer l'environnement local complet:

```powershell
composer dev
```

Cette commande dÃ©marre le serveur Laravel et le worker de queue.

## Configuration

Variables principales:

```env
APP_NAME="Side Project"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=side_project
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database

AZURE_TENANT_ID=
AZURE_CLIENT_ID=
AZURE_CLIENT_SECRET=
AZURE_REDIRECT_URI=http://127.0.0.1:8000/auth/azure/callback
AZURE_ADMIN_GROUP_ID=
AZURE_USER_GROUP_ID=
AZURE_GRAPH_BASE_URL=https://graph.microsoft.com/v1.0

DEMO_ACCESS_ENABLED=false
DEMO_ACCESS_IS_ADMIN=true
DEMO_ACCESS_NAME="Demo User"
DEMO_ACCESS_EMAIL=demo@example.test
```

Si `AZURE_ADMIN_GROUP_ID` ou `AZURE_USER_GROUP_ID` est vide, l'accÃ¨s sera refusÃ© par dÃ©faut aux routes protÃ©gÃ©es.

Pour tester le starter sans Azure en local, mets `DEMO_ACCESS_ENABLED=true`. Un bouton `Explorer en mode demo` apparaitra sur `/login` et ouvrira une session fictive. Garde cette valeur a `false` en production.


## Commandes courantes

```powershell
php artisan migrate
php artisan queue:work --queue=default --tries=3 --timeout=0
php artisan schedule:work
php artisan schedule:list
php artisan route:list
```

Validation rapide:

```powershell
composer test
```

Validation plus complÃ¨te avec dÃ©marrage local:

```powershell
composer final
```

`composer final` est pratique en local ou en staging. En production, il faut plutÃ´t utiliser un vrai superviseur de services pour garder le serveur web, le worker et le scheduler actifs aprÃ¨s un redÃ©marrage.

## DÃ©ploiement

Sur un serveur, prÃ©vois idÃ©alement:

- un serveur web comme IIS, Nginx ou Apache pointant vers `public/`;
- un service supervisÃ© pour `php artisan queue:work --queue=default --tries=3 --timeout=0`;
- un service supervisÃ© ou une tÃ¢che planifiÃ©e pour le scheduler Laravel;
- des permissions d'Ã©criture sur `storage/` et `bootstrap/cache/`;
- une stratÃ©gie de sauvegarde pour la base de donnÃ©es.

AprÃ¨s un dÃ©ploiement:

```powershell
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
cmd /c npm ci
cmd /c npm run build
php artisan queue:restart
```

## Structure de dÃ©part

- `routes/web.php`: routes web principales.
- `routes/console.php`: tÃ¢ches planifiÃ©es.
- `app/Http/Controllers/DashboardController.php`: dashboard.
- `app/Http/Controllers/Admin/HealthDiagnosticController.php`: diagnostic.
- `app/Services/AzureAdminAccessService.php`: vÃ©rification des groupes Azure.
- `resources/views/components/layouts/app.blade.php`: shell visuel principal.
- `resources/views/dashboard.blade.php`: premier Ã©cran applicatif.
- `resources/views/admin/diagnostics.blade.php`: page santÃ©.


## Captures d'écran
![Page de connexion](public/images/Demo%20-%20Login.png)

![Dashboard](public/images/Demo%20-%20Dashboard.png)

![Page de diagnostic](public/images/Demo%20-%20Diagnostic.png)
