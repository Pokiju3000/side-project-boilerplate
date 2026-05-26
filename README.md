# Side Project Boilerplate

Starter Laravel pour petits outils internes avec un visuel poli, une connexion Azure AD, une page de diagnostic, une queue et un scheduler déjà câblés.

L'objectif est de partir vite sans garder de logique métier spécifique au projet source.

## Commandes pour l'installation locale


```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
cmd /c npm install
cmd /c npm run build
```

## Pourquoi

Ce boilerplate sert à démarrer rapidement de petits outils internes Laravel dans un contexte organisationnel : authentification Microsoft, contrôle d'accès par groupes Azure, queue, scheduler, diagnostic applicatif et base visuelle cohérente.

Il est conçu pour démontrer une structure réutilisable, sécuritaire et maintenable, sans inclure de logique métier ni de données provenant d'un projet réel.


## Sécurité et confidentialité

Ce dépôt ne contient aucun code propriétaire, aucune donnée réelle, aucun nom d'organisation interne, aucun secret et aucun endpoint privé.

Les variables sensibles doivent être configurées dans le fichier `.env` et ne doivent jamais être committées.


## Ce qui est volontairement générique

* Aucune logique métier spécifique.
* Aucun modèle de données propre à une organisation.
* Aucun appel vers des systèmes internes.
* Aucun exemple avec des données réelles.
* Authentification Azure configurable par variables d'environnement.


## Inclus

* Laravel 13, Blade, Tailwind CSS et Alpine.js.
* Connexion Microsoft Azure AD via Socialite.
* Mode démo optionnel pour simuler une session sans Azure en local.
* Accès protégé par groupes Azure :
  * `AZURE_ADMIN_GROUP_ID` pour les pages admin.
  * `AZURE_USER_GROUP_ID` pour les utilisateurs réguliers.
* Dashboard minimal.
* Page admin `/admin/diagnostic`.
* Queue database et heartbeat du worker.
* Scheduler Laravel avec une tâche d'entretien de base.
* Scripts Composer pour le setup, le dev, les tests et le déploiement final.

## Prérequis

* PHP 8.3 ou plus récent.
* Composer.
* Node.js et npm.
* Une base de données MySQL, MariaDB, PostgreSQL, SQL Server ou SQLite.
* Une application Azure enregistrée si l'authentification Microsoft est utilisée.





## Captures d'écran
![Page de connexion](public/images/Demo%20-%20Login.png)

![Dashboard](public/images/Demo%20-%20Dashboard.png)

![Page de diagnostic](public/images/Demo%20-%20Diagnostic.png)

