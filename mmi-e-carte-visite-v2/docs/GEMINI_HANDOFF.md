# GEMINI_HANDOFF — Card Builder Phase 3

> **Généré par :** Claude (lead technique)
> **Date :** 2026-09-30
> **Basé sur :** analyse réelle du dépôt `dylanyehouenou/Projet-Carte---de---visite`

---

## GEMINI ROLE

Gemini est responsable de l'**interface utilisateur** du Card Builder et du panel admin étendu.

Gemini construit les vues Blade, les composants JS (Alpine.js + Moveable.js), et les interactions visuelles.

Gemini ne définit pas les routes, ne modifie pas les modèles, et ne touche pas au code MVP existant.

---

## CURRENT STACK

| Élément | Valeur |
|---|---|
| Framework | Laravel 13 |
| PHP | 8.4 |
| CSS | Tailwind CSS v4 (via Vite) |
| JS | Vanilla JS + Alpine.js (pas de SPA React/Vue) |
| Police | Plus Jakarta Sans (déjà chargée via Google Fonts) |
| Couleur principale | `#003189` |
| Couleur secondaire | `#0047c8` |
| Layout admin | `resources/views/layouts/admin.blade.php` |
| Layout public | `resources/views/layouts/app.blade.php` |

---

## EDITOR OBJECTIVE

Créer un éditeur visuel de cartes de visite inspiré de Canva, **spécialisé** cartes de visite.

Structure cible du builder :

```
┌─────────────────────────────────────────────────────────────┐
│  Toolbar (nom carte, statut, Prévisualiser, Enregistrer,    │
│           Publier, Retour)                                  │
├──────────────┬──────────────────────────┬───────────────────┤
│  Sidebar     │  Canvas                  │  Propriétés       │
│  Composants  │                          │                   │
│  ─────────   │   [carte 390×844px]      │  Position x/y     │
│  Texte       │                          │  Taille w/h       │
│  Nom         │                          │  Rotation         │
│  Poste       │                          │  Opacité          │
│  Service     │                          │  Couleur          │
│  Email       │                          │  Police / Taille  │
│  Téléphone   │                          │  Poids            │
│  LinkedIn    │                          │  Alignement       │
│  Calendly    │                          │  Bordure          │
│  ─────────   │                          │  Fond             │
│  Image       │                          │  ─────────        │
│  Logo        │                          │  Calques          │
│  Bannière    │                          │  (z-index list)   │
│  Photo       │                          │                   │
│  QR Code     │                          │                   │
│  ─────────   │                          │                   │
│  Bouton      │                          │                   │
│  Séparateur  │                          │                   │
│  Footer      │                          │                   │
│  Bloc ID     │                          │                   │
└──────────────┴──────────────────────────┴───────────────────┘
```

---

## PAGES

### Nouvelles pages admin

| Route | Vue Blade | Description |
|---|---|---|
| `GET /admin/organizations` | `admin/organizations/index` | Liste organisations |
| `GET /admin/organizations/create` | `admin/organizations/create` | Formulaire création |
| `GET /admin/organizations/{org}/edit` | `admin/organizations/edit` | Formulaire édition |
| `GET /admin/groups` | `admin/groups/index` | Liste groupes |
| `GET /admin/groups/create` | `admin/groups/create` | Formulaire création |
| `GET /admin/groups/{group}/edit` | `admin/groups/edit` | Formulaire édition |
| `GET /admin/cards` | `admin/cards/index` | Liste cartes (statuts) |
| `GET /admin/cards/create` | `admin/cards/create` | Choix du mode (auto/modèle/zéro) |
| `GET /admin/cards/{card}/builder` | `admin/cards/builder` | **Éditeur visuel** |
| `GET /admin/cards/{card}/preview` | `admin/cards/preview` | Prévisualisation |
| `GET /admin/media` | `admin/media/index` | Médiathèque |

### Pages existantes à mettre à jour (navigation uniquement)
- `layouts/admin.blade.php` → ajouter les nouveaux liens dans la navbar

---

## COMPONENTS

### Composants Blade attendus

```
resources/views/components/
├── admin/
│   ├── card-status-badge.blade.php      ← badge draft/published/disabled
│   ├── media-picker.blade.php           ← sélecteur de média (modal)
│   ├── color-picker.blade.php           ← input couleur hex
│   └── card-thumbnail.blade.php        ← aperçu miniature d'une carte
└── builder/
    ├── toolbar.blade.php
    ├── sidebar.blade.php
    ├── canvas.blade.php
    ├── properties-panel.blade.php
    └── layers-panel.blade.php
```

### Composants JS

```
resources/js/builder/
├── canvas.js        ← gestion état CardConfig (objet JS)
├── moveable.js      ← intégration Moveable.js (drag/resize/rotate)
├── properties.js    ← Alpine.js : panneau propriétés
├── sidebar.js       ← glisser-déposer depuis sidebar
└── autosave.js      ← debounce 1500ms → POST /admin/cards/{id}/config
```

---

## CARD CONFIG CONTRACT

Le CardConfig est un objet JSON stocké dans `cards.config`. C'est le seul format d'échange entre le builder et le backend.

```json
{
  "schema_version": 1,
  "canvas": {
    "width": 390,
    "height": 844,
    "background": {
      "type": "color",
      "value": "#FFFFFF",
      "gradient": {
        "from": "#003189",
        "to": "#005deb",
        "direction": "to-tr"
      },
      "media_id": null
    }
  },
  "elements": [
    {
      "id": "uuid-v4",
      "type": "name|job_title|department|email|phone|linkedin|calendly|text|image|logo|banner|photo|qr|button|separator|footer|identity_block",
      "x": 40,
      "y": 200,
      "width": 310,
      "height": 40,
      "rotation": 0,
      "opacity": 1.0,
      "z_index": 10,
      "locked": false,
      "hidden": false,
      "style": {
        "color": "#111827",
        "font_family": "Plus Jakarta Sans",
        "font_size": 16,
        "font_weight": "600",
        "text_align": "left",
        "border_radius": 0,
        "border_width": 0,
        "border_color": null,
        "background_color": null,
        "padding": [0, 0, 0, 0]
      },
      "data": {}
    }
  ]
}
```

**`data` selon le type :**
- `text`, `footer` : `{"content": "Texte"}`
- `name` : `{"format": "full|first|last"}`
- `image`, `logo`, `banner` : `{"media_id": 123}`
- `photo` : `{"fallback": "initials|placeholder"}`
- `qr` : `{"target": "public_url"}`
- `button` : `{"label": "Ajouter à mes contacts", "action": "vcard|url|email|phone", "value": ""}`
- `identity_block` : `{"show_name": true, "show_title": true, "show_dept": true}`
- autres types dynamiques (`job_title`, `email`, etc.) : `{}`

---

## MEDIA CONTRACT

### Upload
```
POST /admin/media
Content-Type: multipart/form-data
Body: file (image), category (logo|banner|photo|image|other), name (string)

Response 201:
{
  "id": 42,
  "name": "Logo MMI'e",
  "url": "/admin/media/42/thumb",
  "original_url": "/admin/media/42",
  "width": 800,
  "height": 200,
  "mime_type": "image/png",
  "size": 45230,
  "category": "logo"
}
```

### Liste
```
GET /admin/media?category=logo&page=1

Response 200:
{
  "data": [...media objects...],
  "meta": {"current_page": 1, "last_page": 3, "total": 52}
}
```

### Thumbnail
```
GET /admin/media/{id}/thumb  → image/png 300×300 (crop center)
GET /admin/media/{id}        → image originale
```

---

## ROUTES

### Nouvelles routes admin (à implémenter par Claude)

```php
// Organizations
Route::resource('organizations', OrganizationController::class);

// Groups
Route::resource('groups', GroupController::class);

// Cards
Route::resource('cards', CardController::class);
Route::get('cards/{card}/builder', [CardController::class, 'builder'])->name('cards.builder');
Route::post('cards/{card}/config', [CardController::class, 'saveConfig'])->name('cards.config.save');
Route::get('cards/{card}/preview', [CardController::class, 'preview'])->name('cards.preview');
Route::post('cards/{card}/publish', [CardController::class, 'publish'])->name('cards.publish');
Route::post('cards/{card}/duplicate', [CardController::class, 'duplicate'])->name('cards.duplicate');

// Media
Route::get('media', [MediaController::class, 'index'])->name('media.index');
Route::post('media', [MediaController::class, 'store'])->name('media.store');
Route::get('media/{media}/thumb', [MediaController::class, 'thumb'])->name('media.thumb');
Route::get('media/{media}', [MediaController::class, 'show'])->name('media.show');
Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
```

---

## SAVE CONTRACT

```
POST /admin/cards/{card}/config
Content-Type: application/json
X-CSRF-TOKEN: {token}

Body:
{
  "config": { ...CardConfig complet... }
}

Response 200:
{
  "success": true,
  "saved_at": "2026-09-30T14:25:00Z"
}

Response 422:
{
  "success": false,
  "errors": ["Invalid element type: foobar", "media_id 999 not found"]
}
```

---

## PREVIEW CONTRACT

```
GET /admin/cards/{card}/preview
GET /admin/cards/{card}/preview?employee_id=42  ← preview avec données réelles

Response: HTML complet (vue Blade rendue)
Status: 200

Note: cette URL est chargée dans une <iframe> dans le builder.
Ajouter ?t={timestamp} pour forcer le rechargement.
```

---

## PUBLICATION CONTRACT

```
POST /admin/cards/{card}/publish
Content-Type: application/json
X-CSRF-TOKEN: {token}

Response 200:
{
  "success": true,
  "status": "published",
  "published_at": "2026-09-30T14:30:00Z"
}

Response 422:
{
  "success": false,
  "message": "La carte doit contenir au moins un élément pour être publiée."
}
```

---

## INTERACTION REQUIREMENTS

### Canvas (Moveable.js)
- **Drag** : déplacer les éléments librement dans le canvas
- **Resize** : redimensionner depuis les poignées (8 points)
- **Rotate** : poignée de rotation
- **Select** : clic simple sur un élément → afficher le panneau propriétés
- **Multi-select** : Shift+clic (ou sélection rectangle) → aligner/grouper
- **Delete** : touche `Delete` ou `Backspace` sur l'élément sélectionné
- **Lock** : élément verrouillé → pas de drag/resize, mais sélectionnable
- **Hidden** : élément masqué → visible dans le builder (semi-transparent), invisible dans la preview

### Sidebar (glisser-déposer)
- Drag d'un composant depuis la sidebar → drop sur le canvas → crée un élément avec valeurs par défaut

### Calques (z-index)
- Liste des éléments dans l'ordre des z-index
- Drag pour réordonner
- Boutons "Avant" / "Arrière" / "Premier plan" / "Arrière-plan"

### Raccourcis clavier
- `Ctrl+Z` : annuler (undo — stack en mémoire JS, pas en DB)
- `Ctrl+Y` : rétablir
- `Ctrl+D` : dupliquer l'élément sélectionné
- `Ctrl+S` : sauvegarder manuellement

---

## RESPONSIVE REQUIREMENTS

| Contexte | Exigence |
|---|---|
| **Builder** (`/admin/cards/{id}/builder`) | Desktop uniquement. Afficher un message "Utilisez un écran d'au moins 1280px" sur mobile. |
| **Pages CRUD** (organizations, groups, cards index) | Responsive — tablettes et desktop |
| **Médiathèque** | Responsive — grille d'aperçu adaptable |
| **Card publique** (`/{slug}`) | Mobile-first, inchangée |

---

## ACCESSIBILITY

- Tous les boutons de la toolbar ont un `aria-label`
- Les inputs du panneau propriétés ont des `<label>` associés
- Focus visible sur tous les éléments interactifs du builder (outline Tailwind `focus:ring-2`)
- La liste des calques est navigable au clavier (`role="listbox"`)

---

## FILES TO MODIFY

```
resources/views/layouts/admin.blade.php     ← ajouter nav items
resources/views/admin/employees/show.blade.php  ← ajouter lien "Assigner une carte"
resources/js/app.js                         ← importer builder JS si sur page builder
resources/css/app.css                       ← styles builder si nécessaire
```

---

## FILES NOT TO MODIFY

```
app/Models/Employee.php                      ← slug/qr_token logic immuable
app/Services/QrCodeService.php               ← ne pas toucher
app/Services/VCardService.php                ← ne pas toucher
app/Services/Wallet/                         ← ne pas toucher
app/Http/Controllers/PublicCardController.php ← ne pas toucher
app/Http/Controllers/WalletController.php    ← ne pas toucher
app/Http/Controllers/PhotoController.php     ← ne pas toucher
resources/views/public/card.blade.php        ← ne pas toucher (sera remplacé en Phase 4)
resources/views/public/disabled.blade.php    ← ne pas toucher
resources/views/auth/login.blade.php         ← ne pas toucher
routes/web.php                               ← les routes /{slug}/* ne changent pas
tests/                                       ← ne pas modifier les tests existants
database/migrations/2026_01_01_000010_create_employees_table.php ← ne pas toucher
```

---

## TEST EXPECTATIONS

Gemini doit écrire des tests Feature pour chaque page créée :

```
GET /admin/organizations          → 200 (admin authentifié)
GET /admin/organizations/create   → 200
POST /admin/organizations         → 302 redirect (validation OK)
POST /admin/organizations         → 422 (validation échouée)
GET /admin/cards/{id}/builder     → 200
GET /admin/cards/{id}/preview     → 200
POST /admin/cards/{id}/config     → 200 (JSON valide)
POST /admin/cards/{id}/config     → 422 (JSON invalide)
POST /admin/cards/{id}/publish    → 200
GET /admin/media                  → 200
POST /admin/media                 → 201 (fichier valide)
POST /admin/media                 → 422 (MIME non autorisé)
```

---

## HANDOFF FORMAT

Quand Gemini retourne ses livrables, le format attendu par Claude est :

```
FEATURE: <ID ex: GEMINI-UI-001>
STATUS: done|partial|blocked
FILES CHANGED:
  - resources/views/admin/cards/builder.blade.php
  - resources/js/builder/canvas.js
  - ...
UI COMPONENTS:
  - Liste des composants créés
DATA USED:
  - CardConfig schema v1
  - Route /admin/cards/{id}/config
BACKEND ASSUMPTIONS:
  - Liste des suppositions faites sur le backend
BLOCKERS:
  - Si blocages, les lister ici
QUESTIONS FOR CLAUDE:
  - Questions techniques sur le backend
TESTING:
  - Tests écrits ou à écrire
NEXT ACTION:
  - Prochaine étape recommandée
```
