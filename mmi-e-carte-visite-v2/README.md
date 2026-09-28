# MMI’e — Cartes de visite virtuelles

Plateforme web permettant de gérer et partager les cartes de visite numériques professionnelles des collaborateurs de la MMI’e.

Flux métier de référence :

Signitic → export CSV → import administrateur → collaborateurs → cartes individuelles

## Architecture

- Laravel 13
- PHP 8.3+
- Blade
- Tailwind CSS
- MariaDB
- Docker Compose
- stockage privé pour les fichiers

## Équipe IA

- GPT : architecture, produit, arbitrage, validation
- Gemini : UX/UI et design
- Claude : développement, sécurité, tests, maintenance

## Règle critique

Chaque carte possède une URL publique stable et un QR stable. Une modification des coordonnées ne doit jamais changer l’URL ni le QR.

## Démarrage

1. Créer le dépôt GitHub privé.
2. Initialiser Laravel 13.
3. Copier les documents `docs/`.
4. Donner à Gemini son brief.
5. Donner à Claude son brief.
6. Travailler par petites fonctionnalités via Pull Requests.
