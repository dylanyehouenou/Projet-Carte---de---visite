# GEMINI-UI-005 — Groupes

---

## FEATURE ID
`GEMINI-UI-005`

---

## OBJECTIVE
Construire les pages CRUD de groupes : liste, création, édition. Un groupe appartient à une organisation et peut avoir son propre logo, ses propres couleurs, et un template de carte par défaut.

---

## CONTEXT
Stack : Laravel 13 / Tailwind CSS v4. Les groupes sont liés aux organisations (table `groups` créée par Claude). Un groupe peut surcharger les couleurs de l'organisation. Le template par défaut du groupe est utilisé lors de la création automatique de cartes pour les collaborateurs de ce groupe.

---

## WHAT EXISTS
- Layout admin : `resources/views/layouts/admin.blade.php`
- Composant `<x-admin.media-picker>` (GEMINI-UI-002)
- Composant `<x-admin.color-picker>` (GEMINI-UI-004)
- Routes CRUD `/admin/groups` (resource créé par Claude)
- Organisations existantes (sélect `<select>`)

---

## WHAT GEMINI MUST BUILD

### 1. `resources/views/admin/groups/index.blade.php`
- Tableau : nom, organisation (badge), couleur primaire (pastille), nb collaborateurs, statut, actions
- Filtre par organisation (select)
- Filtre statut
- Bouton "Nouveau groupe"
- Pagination

### 2. `resources/views/admin/groups/create.blade.php`
Formulaire de création :
- Organisation (select, requis)
- Nom du groupe (requis)
- Description (optionnel)
- Logo → `<x-admin.media-picker category="logo">`
- Couleur primaire (optionnel, override org) → `<x-admin.color-picker>`
- Couleur secondaire (optionnel, override org) → `<x-admin.color-picker>`
- Template de carte par défaut (select liste des card_templates, optionnel)
- Statut (active / inactive)

### 3. `resources/views/admin/groups/edit.blade.php`
Même formulaire que create, pré-rempli. Afficher l'organisation parente en lecture seule ou comme info contextuelle.

### 4. Mise à jour navbar
Ajouter "Groupes" dans `layouts/admin.blade.php`.

---

## DATA CONTRACT

### Formulaire (POST/PUT)
```
organization_id: integer (requis)
name: string (requis)
description: string|null
logo_media_id: integer|null
primary_color: string|null   (#RRGGBB, override organisation)
secondary_color: string|null (#RRGGBB, override organisation)
default_card_template_id: integer|null
status: active|inactive
```

### Réponse liste
```json
{
  "groups": [
    {
      "id": 1,
      "name": "Direction",
      "organization": {"id": 1, "name": "MMI'e"},
      "primary_color": null,
      "status": "active",
      "employees_count": 5
    }
  ]
}
```

---

## BACKEND CONTRACT

```
GET    /admin/groups
GET    /admin/groups/create
POST   /admin/groups
GET    /admin/groups/{group}/edit
PUT    /admin/groups/{group}
DELETE /admin/groups/{group}
```

Les templates disponibles sont passés en `$templates` depuis le contrôleur (list de `card_templates`).
Les organisations disponibles sont passées en `$organizations`.

---

## COMPONENTS

Réutilise :
- `<x-admin.color-picker>` — GEMINI-UI-004
- `<x-admin.media-picker>` — GEMINI-UI-002

---

## INTERACTIONS

- **Organisation** : select → liste toutes les organisations actives
- **Couleurs** : facultatives — si laissées vides, les couleurs de l'organisation parent sont utilisées. Afficher un hint : "Si vide, utilise les couleurs de [Organisation]"
- **Template par défaut** : select → liste les card_templates disponibles + option "Aucun"
- **Filtre organisation** dans l'index : rechargement de la liste

---

## RESPONSIVE

- Formulaires et liste : responsive tablet/desktop (max-w-2xl pour les formulaires)

---

## ACCESSIBILITY

- Select organisation : `<label for="organization_id">Organisation</label>`
- Select template : `<label for="default_card_template_id">Template par défaut</label>`
- Hint couleurs : `<p class="text-sm text-slate-500">Laissez vide pour hériter de l'organisation.</p>`

---

## FILES TO CREATE

```
resources/views/admin/groups/index.blade.php
resources/views/admin/groups/create.blade.php
resources/views/admin/groups/edit.blade.php
```

## FILES TO MODIFY

```
resources/views/layouts/admin.blade.php   ← ajouter "Groupes" dans la navbar
```

---

## DO NOT MODIFY

```
(tous les fichiers MVP listés dans GEMINI_HANDOFF.md)
```

---

## ACCEPTANCE CRITERIA

- [ ] La liste des groupes s'affiche avec filtre par organisation
- [ ] On peut créer un groupe associé à une organisation
- [ ] Les couleurs sont optionnelles (hint visible si vides)
- [ ] Le logo est sélectionnable via media-picker
- [ ] Un template par défaut peut être associé
- [ ] L'édition pré-remplit tous les champs
- [ ] "Groupes" est dans la navbar admin

---

## HANDOFF BACK TO CLAUDE

```
FEATURE: GEMINI-UI-005
STATUS: done|partial|blocked
FILES CHANGED: [liste]
UI COMPONENTS: [liste]
DATA USED: [routes utilisées]
BACKEND ASSUMPTIONS: [suppositions]
BLOCKERS: [si applicable]
QUESTIONS FOR CLAUDE: [questions]
TESTING: [tests écrits]
NEXT ACTION: GEMINI-UI-006 Preview
```
