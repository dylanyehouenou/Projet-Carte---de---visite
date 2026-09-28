# PROTOCOLE DE COLLABORATION IA

## Source de vérité
GitHub privé.

## Autorité
GPT : produit, architecture et arbitrage.
Gemini : design et UX dans les limites approuvées.
Claude : code et maintenance dans les limites approuvées.

## Workflow
1. GPT définit la fonctionnalité et le critère d'acceptation.
2. Gemini conçoit l'interface.
3. Claude implémente.
4. Claude ajoute/actualise les tests.
5. Revue.
6. Pull Request.
7. Fusion.

## IDs
AUTH-xxx
EMP-xxx
CSV-xxx
CARD-xxx
QR-xxx
VCARD-xxx
WALLET-xxx
ADMIN-xxx
NFC-xxx
UI-xxx

## Messages d'échange
FEATURE:
STATUS:
FILES:
BEHAVIOR:
DEPENDENCIES:
RISKS:
TESTS:
NEXT ACTION:

## Règles
- pas de gros changement hors périmètre
- pas de nouvelle dépendance sans raison
- pas de secret dans Git
- pas de suppression automatique de collaborateurs absents d'un import
- pas de changement d'URL/QR sans exigence explicite
