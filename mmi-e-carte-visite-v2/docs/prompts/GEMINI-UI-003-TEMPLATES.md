# GEMINI-UI-003 — Galerie de Templates

---

## FEATURE ID
`GEMINI-UI-003`

---

## OBJECTIVE
Construire la galerie de templates de cartes : page de sélection d'un modèle de départ, avec aperçu, filtrage par catégorie, et action "Utiliser ce modèle" qui crée une copie indépendante.

---

## CONTEXT
Stack : Laravel 13 / Tailwind CSS v4. Les templates sont des enregistrements `card_templates` en base (table créée par Claude). Chaque template a un `config` JSON (CardConfig schema v1), un `thumbnail_media_id`, une `category`, et un flag `is_system` (les templates système ne sont pas supprimables).

**Règle fondamentale :** utiliser un template crée une **copie** indépendante (nouvelle `Card`). Modifier la carte créée ne modifie jamais le template original.

---

## WHAT EXISTS
- Route `GET /admin/cards/create` → affiche le choix de mode (auto / modèle / zéro)
- Route `GET /admin/cards/templates` → liste les templates
- Route `POST /admin/cards/from-template` → `{"template_id": X}` → crée une carte depuis le template, retourne `{"card_id": Y}`
- Route `GET /admin/cards/{id}/builder` → ouvre le builder

---

## WHAT GEMINI MUST BUILD

### 1. `resources/views/admin/cards/create.blade.php`
Page de choix du mode de création :

```
Nouvelle carte — Comment souhaitez-vous commencer ?

┌────────────────┐  ┌────────────────┐  ┌────────────────┐
│  ✨ Automatique │  │  📋 Modèle      │  │  ⬜ De zéro    │
│                │  │                │  │                │
│  Généré depuis │  │  Partir d'un   │  │  Canvas vierge │
│  vos paramètres│  │  modèle existant│  │  Composition   │
│                │  │                │  │  libre         │
└────────────────┘  └────────────────┘  └────────────────┘
```

- Clic "Automatique" → formulaire inline (organisation, groupe, style) → POST /admin/cards/generate
- Clic "Modèle" → redirige vers la galerie de templates `/admin/cards/templates`
- Clic "De zéro" → POST /admin/cards (config vide) → redirect vers builder

### 2. `resources/views/admin/cards/templates.blade.php`
Galerie de templates :
- Filtres par catégorie : Tous / Moderne / Institutionnel / Minimal / Direction / Événement
- Grille de cartes templates : thumbnail 390×844 (redimensionné en 195×422 ou 130×281 dans la grille)
- Sur chaque carte : nom, catégorie, badge "Système" si `is_system`
- Hover → overlay avec bouton "Utiliser ce modèle" + bouton "Aperçu"
- Clic "Aperçu" → modal ou panneau latéral avec preview pleine taille

### 3. Modal aperçu template
- Affiche la preview (iframe `GET /admin/cards/{id}/preview` ou image du thumbnail)
- Bouton "Utiliser ce modèle" → POST /admin/cards/from-template

---

## DATA CONTRACT

### Liste templates
```
GET /admin/cards/templates?category=moderne

Response 200:
{
  "data": [
    {
      "id": 1,
      "name": "Moderne",
      "category": "moderne",
      "is_system": true,
      "thumbnail_url": "/admin/media/5/thumb",
      "description": "Design épuré avec gradient bleu"
    }
  ]
}
```

### Créer depuis template
```
POST /admin/cards/from-template
Body: {"template_id": 1, "name": "Ma carte Direction"}

Response 201:
{
  "card_id": 42,
  "redirect": "/admin/cards/42/builder"
}
```

### Créer de zéro
```
POST /admin/cards
Body: {"name": "Nouvelle carte", "config": null}

Response 201:
{
  "card_id": 43,
  "redirect": "/admin/cards/43/builder"
}
```

---

## BACKEND CONTRACT

```
GET  /admin/cards/templates?category=...
POST /admin/cards/from-template    {"template_id": X, "name": "..."}
POST /admin/cards                  {"name": "...", "config": null}
GET  /admin/cards/{id}/preview     (pour modal aperçu)
```

---

## COMPONENTS

- Grille de templates avec filtres catégorie
- Carte template (thumbnail + nom + catégorie + badge système)
- Modal aperçu (iframe preview + bouton utiliser)
- Formulaire "Créer automatiquement" (organisation, groupe, style)

---

## INTERACTIONS

- **Filtres** : boutons catégorie → rechargement de la grille (fetch ou liens)
- **Hover** sur une carte template → overlay avec boutons
- **Aperçu** → ouvre modal avec preview iframe
- **Utiliser** → POST /from-template → redirect vers builder (avec nom de carte pré-rempli ou dialog demander le nom)
- **De zéro** → POST /cards → redirect builder
- **Automatique** → formulaire inline : sélectionner organisation + groupe → POST /cards/generate

---

## RESPONSIVE

- Galerie templates : 2 colonnes mobile, 3-4 colonnes desktop
- Modal aperçu : max-w-sm (préserver les proportions 390×844)

---

## ACCESSIBILITY

- Images thumbnail : `alt="Template Moderne"`
- Boutons overlay : `aria-label="Aperçu template Moderne"`, `aria-label="Utiliser template Moderne"`
- Modal : `role="dialog"`, fermeture `Escape`

---

## FILES TO CREATE

```
resources/views/admin/cards/create.blade.php
resources/views/admin/cards/templates.blade.php
```

---

## DO NOT MODIFY

```
(tous les fichiers MVP listés dans GEMINI_HANDOFF.md)
```

---

## ACCEPTANCE CRITERIA

- [ ] La page `/admin/cards/create` affiche les 3 modes
- [ ] Clic "Modèle" affiche la galerie
- [ ] Les filtres par catégorie fonctionnent
- [ ] Clic "Utiliser" crée une carte et redirige vers le builder
- [ ] La carte créée depuis un template est indépendante (modifier la carte ne modifie pas le template)
- [ ] Clic "De zéro" crée une carte avec canvas vierge et redirige vers le builder
- [ ] Le mode "Automatique" permet de choisir organisation + groupe

---

## HANDOFF BACK TO CLAUDE

```
FEATURE: GEMINI-UI-003
STATUS: done|partial|blocked
FILES CHANGED: [liste]
UI COMPONENTS: [liste]
DATA USED: [routes utilisées]
BACKEND ASSUMPTIONS: [suppositions]
BLOCKERS: [si applicable]
QUESTIONS FOR CLAUDE: [questions]
TESTING: [tests écrits]
NEXT ACTION: GEMINI-UI-004 Organisations
```
