# ARCHITECTURE TECHNIQUE

## Stack

- Laravel 13
- PHP 8.3+
- Blade
- Tailwind CSS
- MariaDB
- Docker Compose

## Pourquoi un monolithe Laravel

Le produit est principalement un CRUD collaborateurs + import CSV + génération QR/vCard + pages publiques + administration.

Un monolithe Laravel évite de maintenir séparément une SPA React/Next/Vue et une API.

## Services

### app

Application Laravel.

### db

MariaDB.

### web

Nginx ou Caddy selon le serveur.

## Routes

Public :

- `/`
- `/{slug}`
- `/{slug}/vcard`
- `/{slug}/qr`

Admin :

- `/admin/login`
- `/admin`
- `/admin/employees`
- `/admin/employees/{employee}`
- `/admin/imports`

Les routes admin sont protégées.

## Base de données

### users

- id
- name
- email
- password
- role
- is_active
- timestamps

### employees

- id
- external_key
- first_name
- last_name
- job_title
- department
- email
- phone
- postal_address
- website
- photo_path
- linkedin_url
- calendly_url
- slug
- qr_token
- is_active
- deactivated_at
- timestamps

### import_runs

- id
- filename
- imported_by
- rows_total
- rows_created
- rows_updated
- rows_unchanged
- rows_missing
- rows_failed
- report_json
- created_at

### audit_logs

- id
- user_id
- action
- target_type
- target_id
- metadata
- created_at

## Identifiants stables

`external_key` = clé de rapprochement avec Signitic quand disponible.

`slug` = identité URL publique.

`qr_token` = identité du QR.

Lors d’une mise à jour normale :

- ne jamais régénérer `slug` ;
- ne jamais régénérer `qr_token`.

## Fichiers

Les photos sont stockées hors de l’exposition publique directe.

Le chemin de stockage ne doit jamais être utilisé comme URL publique.

## Import

Le service d’import doit produire un rapport :

- créés ;
- mis à jour ;
- inchangés ;
- absents ;
- erreurs.

Absence dans le CSV ≠ suppression automatique.

## Sécurité

- authentification admin ;
- autorisation côté serveur ;
- CSRF ;
- validation stricte des entrées ;
- validation des fichiers ;
- noms de fichiers générés côté serveur ;
- pas de secrets dans Git ;
- logs sans mots de passe/tokens ;
- limite de taille d’upload ;
- échappement HTML ;
- sauvegardes documentées.

## Évolution

Ne pas ajouter Redis, queue, Kubernetes ou stockage objet avant qu’un besoin réel soit identifié.
