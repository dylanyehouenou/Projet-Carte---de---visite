# GEMINI-UI-004 — Organisations

---

## FEATURE ID
`GEMINI-UI-004`

---

## OBJECTIVE
Construire les pages CRUD d'organisations : liste, création, édition. Permettre de configurer l'identité graphique d'une organisation (couleurs, logos) depuis le panel admin.

---

## CONTEXT
Stack : Laravel 13 / Tailwind CSS v4. Les organisations sont de nouvelles entités (table `organizations` créée par Claude). Chaque organisation a des couleurs, des logos (médias), un statut. Les collaborateurs peuvent y être rattachés.

---

## WHAT EXISTS
- Layout admin : `resources/views/layouts/admin.blade.php`
- Composant `<x-admin.media-picker>` (GEMINI-UI-002)
- Routes CRUD `/admin/organizations` (resource créé par Claude)

---

## WHAT GEMINI MUST BUILD

### 1. `resources/views/admin/organizations/index.blade.php`
- Tableau : nom, slug, couleur primaire (pastille), nb collaborateurs, statut, actions
- Bouton "Nouvelle organisation"
- Filtre statut (active / inactive / tous)
- Pagination

### 2. `resources/views/admin/organizations/create.blade.php`
Formulaire de création :
- Nom (requis)
- Description (optionnel)
- Couleur primaire (color picker hex, défaut #003189)
- Couleur secondaire (color picker hex, défaut #0047c8)
- Couleur du texte (color picker hex, défaut #ffffff)
- Logo principal → `<x-admin.media-picker category="logo">`
- Logo secondaire → `<x-admin.media-picker category="logo">`
- Site web (optionnel)
- Adresse (optionnel)
- Statut (active / inactive)

### 3. `resources/views/admin/organizations/edit.blade.php`
Même formulaire que create, pré-rempli. Afficher les logos actuels avec option de remplacement.

### 4. Mise à jour `resources/views/layouts/admin.blade.php`
Ajouter "Organisations" dans la navbar.

---

## DATA CONTRACT

### Formulaire (POST/PUT)
```
name: string (requis)
description: string|null
primary_color: string (#RRGGBB)
secondary_color: string (#RRGGBB)
text_color: string (#RRGGBB)
logo_media_id: integer|null
secondary_logo_media_id: integer|null
website: string|null
address: string|null
status: active|inactive
```

### Réponse liste
```json
{
  "organizations": [
    {
      "id": 1,
      "name": "MMI'e",
      "slug": "mmie",
      "primary_color": "#003189",
      "status": "active",
      "employees_count": 24
    }
  ]
}
```

---

## BACKEND CONTRACT

```
GET    /admin/organizations
GET    /admin/organizations/create
POST   /admin/organizations
GET    /admin/organizations/{org}/edit
PUT    /admin/organizations/{org}
DELETE /admin/organizations/{org}     (désactivation, pas suppression physique si des collaborateurs existent)
```

---

## COMPONENTS

- `<x-admin.color-picker>` — input couleur avec preview pastille et input hex
- `<x-admin.media-picker>` — réutilisé depuis GEMINI-UI-002

---

## INTERACTIONS

- **Color picker** : clic sur la pastille → input `type="color"` natif browser + input texte hex synchronisés
- **Logo** : `<x-admin.media-picker>` s'ouvre en modal → sélection → aperçu du logo affiché dans le formulaire
- **Supprimer organisation** : seulement si aucun collaborateur actif rattaché, sinon désactiver

---

## RESPONSIVE

- Pages organisation : responsive tablet/desktop (formulaire max-w-2xl, tableau scroll horizontal sur mobile)

---

## ACCESSIBILITY

- Labels associés à chaque input
- Pastille couleur : `aria-label="Couleur primaire"`
- Bouton supprimer : `aria-label="Supprimer organisation MMI'e"` + dialog de confirmation

---

## FILES TO CREATE

```
resources/views/admin/organizations/index.blade.php
resources/views/admin/organizations/create.blade.php
resources/views/admin/organizations/edit.blade.php
resources/views/components/admin/color-picker.blade.php
```

## FILES TO MODIFY

```
resources/views/layouts/admin.blade.php   ← ajouter "Organisations" dans la navbar
```

---

## DO NOT MODIFY

```
(tous les fichiers MVP listés dans GEMINI_HANDOFF.md)
```

---

## ACCEPTANCE CRITERIA

- [ ] La liste des organisations s'affiche avec pagination
- [ ] On peut créer une organisation avec couleurs et logo
- [ ] Le color picker hex fonctionne (pastille + input synchronisés)
- [ ] Le logo est sélectionnable via `<x-admin.media-picker>`
- [ ] L'édition pré-remplit tous les champs
- [ ] Le statut est modifiable
- [ ] "Organisations" est dans la navbar admin

---

## HANDOFF BACK TO CLAUDE

```
FEATURE: GEMINI-UI-004
STATUS: done|partial|blocked
FILES CHANGED: [liste]
UI COMPONENTS: [liste]
DATA USED: [routes utilisées]
BACKEND ASSUMPTIONS: [suppositions]
BLOCKERS: [si applicable]
QUESTIONS FOR CLAUDE: [questions]
TESTING: [tests écrits]
NEXT ACTION: GEMINI-UI-005 Groupes
```
