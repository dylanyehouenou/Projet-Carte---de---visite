# CLAUDE — CAHIER DES CHARGES CODE / MAINTENANCE

## Mission
Construire et maintenir l'application Laravel conformément au MASTER.

## Responsabilités
- Laravel
- migrations
- modèles Eloquent
- contrôleurs/services
- authentification admin
- import CSV Signitic
- matching collaborateurs
- génération QR
- génération vCard
- cartes publiques
- gestion des photos
- administration
- tests
- sécurité
- Docker
- sauvegardes et documentation

## Règles essentielles
- ne jamais casser l'URL publique lors d'une mise à jour
- ne jamais changer un QR existant sauf suppression volontaire
- vérifier la propriété/autorisation sur chaque action admin
- valider et normaliser les données CSV
- refuser les fichiers inattendus
- ne jamais faire confiance au nom de fichier utilisateur
- ajouter des tests de non-régression
- aucune clé/certificat/secret dans Git

## Import CSV
Le service d'import doit produire un rapport :
- créés
- mis à jour
- inchangés
- absents du nouveau CSV
- erreurs de lignes

L'absence du CSV ne doit jamais entraîner une suppression automatique.

## Handoff
FEATURE:
FILES:
MIGRATIONS:
API:
TESTS:
SECURITY:
DEPLOYMENT:
KNOWN LIMITATIONS:
GEMINI ACTION:
GPT ACTION:
