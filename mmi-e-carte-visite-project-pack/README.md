# MMI’e — Cartes de visite virtuelles

Projet interne de génération et de gestion de cartes de visite numériques professionnelles pour les collaborateurs de la MMI’e.

## Référence
Le cahier des charges source est le document métier fourni par la MMI’e.

## Architecture retenue
Pour rester simple à déployer et maintenir :
- Laravel (PHP)
- Blade + Tailwind CSS
- SQLite en première version
- Stockage local des fichiers
- Nginx ou Caddy en reverse proxy selon l'infrastructure
- Docker Compose pour rendre le déploiement reproductible

Cette architecture peut évoluer vers PostgreSQL sans réécrire le métier si le besoin apparaît.

## Principe
Signitic -> export CSV -> import administrateur -> données collaborateurs -> cartes individuelles

Les cartes ont une URL permanente. Le QR code pointe vers cette URL, jamais directement vers les coordonnées.

## Équipe IA
- GPT : architecture, arbitrages, spécifications, validation
- Gemini : UX/UI et identité visuelle dans le cadre validé
- Claude : développement, tests, sécurité, maintenance et documentation

## Priorité
MVP simple et fiable avant les intégrations avancées.
