# PROTOCOLE MULTI-IA

## Source de vérité

Le dépôt GitHub privé est la source de vérité technique.

Priorité :

1. `docs/01_MASTER.md`
2. `docs/02_ARCHITECTURE.md`
3. briefs spécialisés
4. code existant

## Autorité

GPT :
- produit ;
- architecture ;
- arbitrage ;
- validation.

Gemini :
- design ;
- UX ;
- UI.

Claude :
- code ;
- tests ;
- sécurité ;
- maintenance.

## Règles

- aucune modification majeure silencieuse ;
- aucun secret dans Git ;
- aucune suppression automatique des collaborateurs absents du CSV ;
- aucune régénération automatique d’URL ou QR ;
- tout changement critique doit être documenté.

## IDs

AUTH-xxx
EMP-xxx
CSV-xxx
CARD-xxx
QR-xxx
VCARD-xxx
ADMIN-xxx
WALLET-xxx
NFC-xxx
UI-xxx
INFRA-xxx

## Handoff

FEATURE:
STATUS:
FILES:
BEHAVIOR:
DEPENDENCIES:
API/BACKEND:
DATABASE:
TESTS:
RISKS:
NEXT ACTION:

## Cycle

Spécification → Design → Code → Tests → Pull Request → Revue → Merge → Validation.
