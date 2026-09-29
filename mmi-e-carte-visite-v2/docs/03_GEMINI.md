# GEMINI — CAHIER DE CHARGES DESIGN / UX / UI

## Mission

Concevoir l’expérience utilisateur et l’interface de la plateforme MMI’e Cartes de visite virtuelles.

## Priorités

1. conformité à la charte MMI’e ;
2. simplicité ;
3. clarté ;
4. mobile-first pour la carte publique ;
5. accessibilité ;
6. cohérence des composants ;
7. performance.

## Écrans publics

- carte collaborateur ;
- carte désactivée ;
- état d’erreur propre.

## Écrans administration

- connexion ;
- dashboard ;
- liste des collaborateurs ;
- fiche collaborateur ;
- import CSV ;
- résultat d’import ;
- aperçu d’une carte ;
- compte/paramètres.

## Composants

- identité collaborateur ;
- boutons contact ;
- boutons conditionnels ;
- QR ;
- badges ;
- tableau ;
- recherche ;
- filtres ;
- formulaires ;
- alertes ;
- modales ;
- états vide ;
- chargement ;
- erreur ;
- succès.

## Contraintes

- ne pas inventer de contenu métier ;
- ne pas afficher de bouton sans donnée ;
- ne pas modifier l’architecture ;
- ne pas remplacer Blade/Tailwind par une SPA ;
- conserver des composants réutilisables.

## Handoff

FEATURE: Refonte UI/UX complète (Côté public & Back-office Admin) avec Tailwind CSS v4.
PAGE: Carte publique, Carte désactivée, Erreur 404, Login, Dashboard Admin, Gestion Collaborateurs (Index, Show, Edit), Gestion Imports (Index, Create, Show).
COMPONENTS: Cartes profils, bannières avec logo, tableaux de données responsives, formulaires, alertes de succès/erreur, stat cards.
FILES: `public/card.blade.php`, `public/disabled.blade.php`, `errors/404.blade.php`, `layouts/app.blade.php`, `layouts/admin.blade.php`, `auth/login.blade.php`, `admin/dashboard.blade.php`, `admin/employees/index.blade.php`, `admin/employees/show.blade.php`, `admin/employees/edit.blade.php`, `admin/imports/index.blade.php`, `admin/imports/create.blade.php`, `admin/imports/show.blade.php`.
BEHAVIOR: Transitions au survol, animations au clic (active:scale), confirmation native (JS confirm) sur les boutons destructeurs, preview du nom de fichier CSV via JS natif.
API/BACKEND DEPENDENCIES: Toutes les variables dynamiques existantes, routes backend, paginations et directives Blade conservées à l'identique.
RESPONSIVE NOTES: Approche mobile-first globale. Vues publiques capées à `max-w-sm`. Interface Admin capée à `max-w-7xl` avec des grilles (`grid-cols-1 sm:grid-cols-2 lg:grid-cols-3`) adaptatives.
ACCESSIBILITY: Police *Plus Jakarta Sans* lisible, contrastes renforcés (textes `slate-500` à `slate-900`), états de focus visibles sur les champs de formulaire (`focus:ring`).
CLAUDE ACTION: Intégrer les fichiers `.blade.php` fournis sans toucher aux contrôleurs. Aucun ajustement backend n'est requis si les variables Blade n'ont pas été modifiées côté serveur.
