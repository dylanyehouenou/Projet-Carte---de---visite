# ARCHITECTURE TECHNIQUE

## Choix
Laravel + Blade + Tailwind + SQLite.

### Pourquoi
Le projet est principalement un outil CRUD + import CSV + génération de documents/liens.
Une application Laravel monolithique évite de multiplier frontend, backend, services et bases de données.

## Modules
1. Auth/Admin
2. Collaborateurs
3. Import CSV
4. Cartes publiques
5. QR
6. vCard
7. Wallet
8. Paramètres
9. Journal d'audit

## Modèle de données
### users
id, name, email, password_hash, role, timestamps

### employees
id, external_key, first_name, last_name, job_title, department, email, phone, postal_address, website, photo_path, linkedin_url, calendly_url, slug, qr_token, is_active, created_at, updated_at

### import_runs
id, filename, imported_by, created_at, counts_json

### audit_logs
id, user_id, action, target_type, target_id, metadata_json, created_at

## Identité
`external_key` sert à faire correspondre les lignes Signitic.
`slug` est l'identité URL publique.
`qr_token` reste stable tant que la carte existe.

## Règle de stabilité
Une mise à jour CSV modifie les données de l'employé mais ne régénère ni `slug` ni `qr_token`.

## Fichiers
Les photos et éventuels fichiers de Wallet sont stockés hors du répertoire public, avec accès contrôlé.

## Déploiement
Docker Compose avec :
- app PHP/Laravel
- web server
- volume storage
- SQLite

Une base PostgreSQL pourra être ajoutée plus tard si le volume ou la stratégie de sauvegarde l'exige.
