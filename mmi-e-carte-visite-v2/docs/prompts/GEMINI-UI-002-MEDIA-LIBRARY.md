# GEMINI-UI-002 — Médiathèque

---

## FEATURE ID
`GEMINI-UI-002`

---

## OBJECTIVE
Construire la médiathèque admin : page de gestion des médias (upload, browse, catégorisation, aperçu, suppression) et le composant "sélecteur de média" utilisé dans le Card Builder pour associer une image à un élément.

---

## CONTEXT
Stack : Laravel 13 / Tailwind CSS v4 / Alpine.js. La médiathèque est une page admin standard (responsive) + un composant modal réutilisable dans le builder.

Les médias sont stockés sur le disk `local` (volume Docker). Le backend génère un thumbnail 300×300 à l'upload. MIME vérifié côté serveur — le frontend n'a pas à valider le format, mais doit afficher les erreurs retournées.

---

## WHAT EXISTS
- Layout admin : `resources/views/layouts/admin.blade.php`
- Route `GET /admin/media` → liste
- Route `POST /admin/media` → upload
- Route `GET /admin/media/{id}/thumb` → thumbnail 300×300
- Route `DELETE /admin/media/{id}` → suppression (bloquée si utilisé)

---

## WHAT GEMINI MUST BUILD

### 1. `resources/views/admin/media/index.blade.php`
- Grille de médias (3-4 colonnes desktop, 2 colonnes tablet)
- Filtres : catégorie (logo / bannière / photo / image / autre), recherche par nom
- Chaque carte média : thumbnail, nom, poids, catégorie, bouton supprimer
- Bouton "Uploader un média" → ouvre le formulaire d'upload (inline ou modal)
- Pagination

### 2. `resources/views/components/admin/media-picker.blade.php`
- Composant Alpine.js utilisable dans le builder : `<x-admin.media-picker :category="'logo'" @selected="onMediaSelected($event)">`
- Déclenché par un bouton "Choisir un média" dans le panneau propriétés du builder
- Ouvre une modal avec la grille de médias filtrée par catégorie
- Clic sur un média → émet l'event avec `{id, url, width, height, name}`
- Upload inline dans la modal pour ajouter un nouveau média sans quitter le builder

### 3. `resources/views/admin/media/upload.blade.php` (ou section inline)
- Formulaire d'upload : champ fichier, catégorie, nom
- Affiche les erreurs de validation retournées par le backend
- Formats acceptés côté UI (informatif seulement, la validation réelle est serveur) : PNG, JPEG, WebP

---

## DATA CONTRACT

### Upload request
```
POST /admin/media
Content-Type: multipart/form-data
Body:
  file: (binary)
  category: logo|banner|photo|image|other
  name: string (optionnel, fallback = nom original)
```

### Upload response 201
```json
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

### Upload response 422
```json
{
  "errors": {
    "file": ["Le format de fichier n'est pas autorisé."],
    "category": ["La catégorie est requise."]
  }
}
```

### Liste response 200
```json
{
  "data": [
    {
      "id": 42,
      "name": "Logo MMI'e",
      "url": "/admin/media/42/thumb",
      "width": 800, "height": 200,
      "size": 45230,
      "category": "logo",
      "created_at": "2026-09-30T10:00:00Z"
    }
  ],
  "meta": {"current_page": 1, "last_page": 3, "total": 52}
}
```

### Delete response 200
```json
{"success": true}
```

### Delete response 409 (utilisé)
```json
{"success": false, "message": "Ce média est utilisé par une ou plusieurs cartes."}
```

---

## BACKEND CONTRACT

```
GET    /admin/media?category=logo&search=mmie&page=1
POST   /admin/media
GET    /admin/media/{id}/thumb   → image/png 300×300
GET    /admin/media/{id}         → image originale (headers Content-Disposition)
DELETE /admin/media/{id}
```

---

## COMPONENTS

- `<x-admin.media-picker>` — sélecteur modal, filtre par catégorie, émit event Alpine
- Grille de miniatures avec overlay hover (nom, poids, bouton supprimer)
- Badge catégorie coloré sur chaque miniature

---

## INTERACTIONS

- **Upload** : drag-and-drop de fichier sur la zone upload OU clic pour parcourir
- **Filtre catégorie** : boutons radio inline (Tous / Logos / Bannières / Photos / Images / Autres)
- **Recherche** : input text, filtre en temps réel (fetch debounce 300ms) ou submit
- **Supprimer** : bouton poubelle → confirm dialog → DELETE → retrait de la grille
- **Sélection (dans le picker modal)** : hover → outline bleu → clic → ferme modal + émet event
- **Pagination** : simple pagination Blade (boutons Précédent / Suivant)

---

## RESPONSIVE

- Page `/admin/media` : responsive, grille 2 colonnes mobile, 4 colonnes desktop
- Modal media-picker : max-w-3xl, scrollable sur mobile

---

## ACCESSIBILITY

- `<img>` dans la grille : `alt="Nom du média"`
- Bouton supprimer : `aria-label="Supprimer Logo MMI'e"`
- Modal picker : `role="dialog"`, `aria-modal="true"`, focus trap, fermeture `Escape`

---

## FILES TO CREATE

```
resources/views/admin/media/index.blade.php
resources/views/components/admin/media-picker.blade.php
```

---

## DO NOT MODIFY

```
(tous les fichiers MVP listés dans GEMINI_HANDOFF.md)
```

---

## ACCEPTANCE CRITERIA

- [ ] La page `/admin/media` affiche la grille de médias
- [ ] On peut uploader une image PNG/JPEG/WebP
- [ ] Une erreur est affichée si le format est refusé par le serveur
- [ ] Le filtre par catégorie fonctionne
- [ ] La suppression d'un média non utilisé fonctionne
- [ ] La suppression d'un média utilisé affiche un message d'erreur (409)
- [ ] Le composant `<x-admin.media-picker>` s'ouvre en modal depuis le builder
- [ ] Sélectionner un média dans la modal émet l'event avec l'ID et l'URL du média
- [ ] On peut uploader un nouveau média directement depuis la modal du picker

---

## HANDOFF BACK TO CLAUDE

```
FEATURE: GEMINI-UI-002
STATUS: done|partial|blocked
FILES CHANGED: [liste]
UI COMPONENTS: [liste]
DATA USED: [routes utilisées]
BACKEND ASSUMPTIONS: [suppositions]
BLOCKERS: [si applicable]
QUESTIONS FOR CLAUDE: [questions]
TESTING: [tests écrits]
NEXT ACTION: GEMINI-UI-003 Templates
```
