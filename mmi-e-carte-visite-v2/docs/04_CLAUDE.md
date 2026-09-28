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

## Handoff

FEATURE:
FILES:
MIGRATIONS:
DEPENDENCIES:
TESTS:
SECURITY:
DEPLOYMENT:
KNOWN_LIMITATIONS:
GEMINI ACTION:
GPT ACTION:
