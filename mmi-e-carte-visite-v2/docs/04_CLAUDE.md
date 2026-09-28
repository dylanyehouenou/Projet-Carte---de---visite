# CLAUDE — CAHIER DE CHARGES CODE / MAINTENANCE

## Mission

Construire, tester, sécuriser et maintenir le projet conformément au MASTER.

## Stack imposée

- Laravel 13
- PHP 8.3+
- Blade
- Tailwind CSS
- MariaDB
- Docker Compose

## Modules

- authentification ;
- collaborateurs ;
- import CSV ;
- carte publique ;
- QR ;
- vCard ;
- LinkedIn ;
- Calendly ;
- activation/désactivation ;
- audit ;
- Wallet providers séparés.

## Ordre

1. socle Laravel ;
2. Docker/MariaDB ;
3. authentification ;
4. modèles/migrations ;
5. CRUD collaborateurs ;
6. carte publique ;
7. QR stable ;
8. vCard ;
9. import CSV ;
10. activation/désactivation ;
11. audit ;
12. UI finale ;
13. Wallet ;
14. étude NFC.

## Règles

- Form Requests pour valider ;
- Policies pour autoriser ;
- services métier pour les traitements importants ;
- migrations versionnées ;
- tests Feature/Unit ;
- pas de secrets dans Git ;
- pas de nouvelle dépendance sans justification ;
- aucun delete automatique suite à une absence CSV ;
- aucune modification involontaire de l’URL ou du QR.

## Import

Créer un service d’import dédié.

Il doit gérer :

- create ;
- update ;
- unchanged ;
- missing ;
- failure.

Il doit retourner un rapport exploitable par l’administration.

## QR/URL

À la création :

- générer slug ;
- générer qr_token.

Ensuite, une mise à jour normale ne les change jamais.

## vCard

Générer la vCard à partir des données réellement présentes.

Ne pas produire de champs inutiles ou de boutons vides.

## Tests minimaux

- création ;
- URL stable ;
- QR stable ;
- modification sans changement URL/QR ;
- carte désactivée ;
- carte publique ;
- vCard ;
- import create/update/unchanged/missing/failure ;
- autorisations admin.

## Handoff — MVP livré le 2026-09-28

FEATURE: MVP complet — auth admin, CRUD collaborateurs, import CSV, carte publique, QR stable, vCard 3.0, activation/désactivation, audit log, Tailwind build.

FILES:
- app/Models/Employee.php — slug + qr_token générés UNIQUEMENT au boot creating
- app/Services/CsvImportService.php — import CSV ; create/update/unchanged/missing/failure ; jamais delete
- app/Services/VCardService.php — vCard 3.0 sans champs vides
- app/Services/QrCodeService.php — PNG via qrencode ; fallback GD
- app/Services/AuditService.php — audit_logs
- app/Http/Controllers/Admin/ — DashboardController, EmployeeController, ImportController
- app/Http/Controllers/PublicCardController.php — show (410 si désactivé), vcard, qrPng
- app/Http/Controllers/PhotoController.php — photos via storage
- app/Http/Middleware/AdminAuth.php — role=admin + is_active requis
- bootstrap/app.php — redirectGuestsTo(admin.login) ; CSRF désactivé en test via PreventRequestForgery
- routes/web.php — ordre critique : auth → admin → accueil → wildcards EN DERNIER

MIGRATIONS:
- 0001_01_01_000000_create_users_table — +role +is_active
- 2026_01_01_000010_create_employees_table — slug/qr_token uniques
- 2026_01_01_000011_create_import_runs_table
- 2026_01_01_000012_create_audit_logs_table

DEPENDENCIES: laravel/framework 13, simple-qrcode via qrencode shell, GD, SQLite pour tests

TESTS: 35/35 passants — AuthTest, EmployeeTest, CsvImportTest, PublicCardTest, VCardTest, QrTest, ExampleTest

SECURITY:
- CSRF actif en prod (PreventRequestForgery dans le groupe web)
- Photos servies via PhotoController (jamais exposées directement)
- Policies + Form Requests sur toutes les actions admin
- Jamais de secrets dans Git (.env ignoré, .env.docker en exemple)

DEPLOYMENT:
```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan migrate --seed
docker compose exec app npm run build
# Admin : http://localhost:8080/admin  login: admin@mmi-e.fr / password
```

KNOWN_LIMITATIONS:
- QR génération via shell qrencode (disponible dans le Dockerfile) ; fallback matrice placeholder si absent
- Wallet Apple / NFC hors scope MVP
- Pas d'envoi d'email automatique à la création d'un collaborateur (hors scope MVP)

GEMINI ACTION: Révision UI/UX de resources/views/public/card.blade.php ; validation accessibilité mobile ; charte graphique MMI'e (#003189)

GPT ACTION: Revue architecture pour phase 2 (NFC, Wallet, multi-établissements) ; rédaction cahier des charges évolutions
