# GEMINI-UI-001 — Card Builder (Éditeur Visuel)

---

## FEATURE ID
`GEMINI-UI-001`

---

## OBJECTIVE
Construire l'éditeur visuel Card Builder : page admin permettant de composer une carte de visite numérique par drag/drop/resize/rotate sur un canvas, avec panneau de propriétés et autosave.

---

## CONTEXT
Le projet MMI'e est un Laravel 13 / PHP 8.4 / Tailwind CSS v4 / Alpine.js. Aucune SPA React/Vue. Le builder est une page Blade enrichie de JS vanilla + Alpine.js + Moveable.js (MIT, ~70kb, drag/resize/rotation sans framework).

La carte est décrite par un objet JSON (CardConfig schema v1) stocké en base. Chaque modification dans le builder met à jour cet objet JS en mémoire, puis l'autosave l'envoie au backend via fetch.

---

## WHAT EXISTS
- Layout admin : `resources/views/layouts/admin.blade.php` (navbar: Dashboard | Collaborateurs | Imports CSV)
- Police : Plus Jakarta Sans, couleur principale #003189, secondaire #0047c8
- Tailwind CSS v4 via Vite
- Route `GET /admin/cards/{card}/builder` → à brancher sur la vue à créer
- Route `POST /admin/cards/{card}/config` → reçoit `{"config": {...CardConfig...}}`
- Route `GET /admin/cards/{card}/preview` → retourne HTML de la carte (pour iframe)

---

## WHAT GEMINI MUST BUILD

### 1. `resources/views/admin/cards/builder.blade.php`
Page complète (étend un layout minimaliste ou `layouts/admin.blade.php` modifié pour full-screen builder).

Structure :
```
┌──────────────────────────────────────────────────────────────┐
│ Toolbar                                                      │
├──────────────┬───────────────────────────┬───────────────────┤
│ Sidebar      │ Canvas                    │ Propriétés        │
│ (composants) │ (390×844px, scrollable)   │ (élément sélect.) │
│              │                           │                   │
│              │                           │ Calques (z-index) │
└──────────────┴───────────────────────────┴───────────────────┘
```

### 2. `resources/js/builder/canvas.js`
- État `cardConfig` (objet JS calqué sur CardConfig schema v1)
- Fonctions : `addElement(type)`, `updateElement(id, patch)`, `removeElement(id)`, `reorderElement(id, direction)`, `getConfig()` → sérialise pour le backend

### 3. `resources/js/builder/moveable.js`
- Intégration Moveable.js sur les éléments du canvas
- Events : `onDrag`, `onResize`, `onRotate` → appelle `canvas.updateElement()`

### 4. `resources/js/builder/autosave.js`
- Debounce 1500ms après toute modification → `POST /admin/cards/{id}/config`
- Feedback visuel : "Enregistrement..." / "Enregistré à HH:MM" dans la toolbar

### 5. `resources/js/builder/properties.js` (Alpine.js)
- Panneau propriétés réactif (x, y, w, h, rotation, opacity, style, data)
- Mis à jour quand un élément est sélectionné

### 6. `resources/views/admin/cards/create.blade.php`
- Modal/page de choix : "Créer automatiquement" | "Partir d'un modèle" | "Créer de zéro"

---

## DATA CONTRACT

```json
{
  "schema_version": 1,
  "canvas": {
    "width": 390,
    "height": 844,
    "background": {
      "type": "color",
      "value": "#FFFFFF",
      "gradient": {"from": "#003189", "to": "#005deb", "direction": "to-tr"},
      "media_id": null
    }
  },
  "elements": [
    {
      "id": "uuid-v4",
      "type": "name|job_title|department|email|phone|linkedin|calendly|text|image|logo|banner|photo|qr|button|separator|footer|identity_block",
      "x": 40, "y": 200, "width": 310, "height": 40,
      "rotation": 0, "opacity": 1.0, "z_index": 10,
      "locked": false, "hidden": false,
      "style": {
        "color": "#111827", "font_family": "Plus Jakarta Sans",
        "font_size": 16, "font_weight": "600", "text_align": "left",
        "border_radius": 0, "border_width": 0, "border_color": null,
        "background_color": null, "padding": [0, 0, 0, 0]
      },
      "data": {}
    }
  ]
}
```

`data` selon le type :
- `text`, `footer` : `{"content": "..."}`
- `name` : `{"format": "full|first|last"}`
- `image`, `logo`, `banner` : `{"media_id": 123}`
- `photo` : `{"fallback": "initials|placeholder"}`
- `qr` : `{"target": "public_url"}`
- `button` : `{"label": "...", "action": "vcard|url|email|phone", "value": ""}`
- `identity_block` : `{"show_name": true, "show_title": true, "show_dept": true}`
- autres : `{}`

---

## BACKEND CONTRACT

```
GET  /admin/cards/{card}/builder
     → Blade: passe $card (avec $card->config JSON) et $card->name à la vue

POST /admin/cards/{card}/config
     Body: {"config": {...CardConfig...}}
     Response 200: {"success": true, "saved_at": "ISO8601"}
     Response 422: {"success": false, "errors": ["..."]}

GET  /admin/cards/{card}/preview
GET  /admin/cards/{card}/preview?t={timestamp}
     → HTML complet (pour iframe, pas de layout admin)

POST /admin/cards/{card}/publish
     Response 200: {"success": true, "status": "published", "published_at": "ISO8601"}
```

---

## COMPONENTS

- `<x-admin.card-status-badge>` — badge couleur (draft=gris, published=vert, disabled=rouge)
- `<x-builder.toolbar>` — toolbar avec nom, statut, boutons
- `<x-builder.sidebar>` — liste des composants disponibles, draggable
- `<x-builder.canvas>` — zone de drop, éléments positionnés en absolute
- `<x-builder.properties-panel>` — formulaires propriétés (Alpine.js)
- `<x-builder.layers-panel>` — liste des calques (z-index), réordonnable

---

## INTERACTIONS

- **Drag** depuis sidebar → drop canvas → `addElement(type)` avec position de drop
- **Select** → clic sur élément → panneau propriétés se met à jour
- **Drag** élément → `onDrag` → `updateElement(id, {x, y})`
- **Resize** → `onResize` → `updateElement(id, {width, height})`
- **Rotate** → `onRotate` → `updateElement(id, {rotation})`
- **Delete** → `Delete`/`Backspace` → `removeElement(id)`
- **Lock** → bouton cadenas dans propriétés → `updateElement(id, {locked: true})`
- **Hide** → bouton œil → `updateElement(id, {hidden: true})` (semi-transparent dans builder)
- **Z-index** → boutons avant/arrière dans le panel calques
- **Undo/Redo** → `Ctrl+Z` / `Ctrl+Y` — stack en mémoire JS (pas en DB)
- **Duplicate** → `Ctrl+D` → copie l'élément avec offset +20px x/y, nouvel UUID

---

## RESPONSIVE

- Builder : **desktop uniquement** (min-width: 1280px). En-dessous, afficher :
  ```
  "Le Card Builder nécessite un écran d'au moins 1280px.
   Utilisez un ordinateur pour créer vos cartes."
  ```
- Pages CRUD (organizations, cards index) : responsive tablet/desktop

---

## ACCESSIBILITY

- Tous les boutons toolbar ont `aria-label`
- Inputs panneau propriétés : `<label for="...">` associé
- Focus visible sur éléments interactifs (`focus:ring-2 focus:ring-blue-500`)
- Panel calques : `role="list"`, items `role="listitem"` avec `tabindex`

---

## FILES TO CREATE

```
resources/views/admin/cards/builder.blade.php
resources/views/admin/cards/create.blade.php
resources/views/admin/cards/index.blade.php
resources/views/components/builder/toolbar.blade.php
resources/views/components/builder/sidebar.blade.php
resources/views/components/builder/canvas.blade.php
resources/views/components/builder/properties-panel.blade.php
resources/views/components/builder/layers-panel.blade.php
resources/views/components/admin/card-status-badge.blade.php
resources/js/builder/canvas.js
resources/js/builder/moveable.js
resources/js/builder/autosave.js
resources/js/builder/properties.js
```

---

## DO NOT MODIFY

```
app/Models/Employee.php
app/Services/QrCodeService.php
app/Services/VCardService.php
app/Services/Wallet/
app/Http/Controllers/PublicCardController.php
app/Http/Controllers/WalletController.php
resources/views/public/card.blade.php
resources/views/public/disabled.blade.php
routes/web.php (routes /{slug}/*)
tests/
database/migrations/2026_01_01_000010_create_employees_table.php
```

---

## ACCEPTANCE CRITERIA

- [ ] La page builder se charge sans erreur JS console
- [ ] On peut ajouter un élément `name` par drag depuis la sidebar
- [ ] L'élément est déplaçable et redimensionnable avec Moveable.js
- [ ] Le panneau propriétés affiche les valeurs de l'élément sélectionné
- [ ] La modification d'une propriété met à jour l'élément visuellement
- [ ] L'autosave se déclenche 1500ms après la dernière modification
- [ ] La toolbar affiche "Enregistré à HH:MM" après autosave réussi
- [ ] La preview (iframe) se recharge après autosave
- [ ] Le bouton "Publier" appelle POST /publish et met à jour le badge statut
- [ ] Ctrl+Z annule la dernière action
- [ ] Sur écran < 1280px, un message de fallback s'affiche

---

## HANDOFF BACK TO CLAUDE

```
FEATURE: GEMINI-UI-001
STATUS: done|partial|blocked
FILES CHANGED: [liste exhaustive]
UI COMPONENTS: [liste]
DATA USED: [CardConfig schema v1, routes utilisées]
BACKEND ASSUMPTIONS: [ce que Gemini suppose du backend]
BLOCKERS: [si applicable]
QUESTIONS FOR CLAUDE: [questions techniques]
TESTING: [tests écrits]
NEXT ACTION: GEMINI-UI-002 Médiathèque
```
