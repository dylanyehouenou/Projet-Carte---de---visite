# PROMPTS CLAUDE

## INITIALISATION

Tu es le développeur principal du projet MMI’e Cartes de visite virtuelles.

Lis d’abord :

- docs/01_MASTER.md
- docs/02_ARCHITECTURE.md
- docs/04_CLAUDE.md
- docs/05_AI_HANDOFF.md
- docs/06_BACKLOG.md

Initialise un projet Laravel 13 avec PHP 8.3+.

Stack imposée :
- Laravel ;
- Blade ;
- Tailwind CSS ;
- MariaDB ;
- Docker Compose.

N’introduis pas React, Next.js, Vue SPA, Kubernetes, Redis ou microservices au MVP.

Construis d’abord :
- bootstrap Laravel ;
- Docker ;
- MariaDB ;
- authentification admin ;
- migrations ;
- structure des services ;
- tests ;
- documentation.

Ne commence pas Wallet/NFC avant le cœur du produit.

## IMPORT CSV

Implémente CSV-001 à CSV-008.

Inspecte les colonnes réellement disponibles dans le CSV.
Ne prétends pas connaître des colonnes qui n’ont pas été fournies.
Si le mapping doit être configurable, construis-le explicitement.

Le système doit :
- créer ;
- mettre à jour ;
- laisser inchangé ;
- signaler absent ;
- rapporter les erreurs.

Aucune suppression automatique.

Une modification ne doit jamais changer slug ou qr_token.

Ajoute les tests.

## CARTE

Implémente :
- CARD-001 à CARD-004 ;
- QR-001 à QR-003 ;
- VCARD-001 à VCARD-002.

Respecte :
- URL stable ;
- QR stable ;
- champs conditionnels ;
- carte désactivée ;
- vCard ;
- aucun bouton vide.

## AUDIT FINAL

Effectue un audit sans réécrire inutilement le projet.

Vérifie :
- sécurité ;
- autorisation ;
- uploads ;
- CSV ;
- URL/QR ;
- logs ;
- migrations ;
- sauvegardes ;
- Docker ;
- tests ;
- exposition des fichiers.

Pour chaque problème :

SEVERITY:
FILE:
PROBLEM:
FIX:
TEST:
