# CARD_BUILDER_ARCHITECTURE — Phase 3

> **Date :** 2026-09-30
> **Auteur :** Claude (lead technique)
> **Stack :** Laravel 13 · PHP 8.4 · Tailwind CSS v4 · MariaDB 11 · Docker

---

## 1. Product Scope

Le produit évolue d'un système de cartes hardcodées vers un **Card Builder** : éditeur visuel administrable permettant de créer, personnaliser et publier des cartes de visite numériques.

**Trois modes de création :**
1. **Automatique** — génération depuis les paramètres de l'organisation/groupe
2. **Modèle** — partir d'un template existant (clonable, non destructif)
3. **Zéro** — canvas vierge, composition libre

**Périmètre fonctionnel :**
- Multi-organisations, multi-groupes (tout en DB, zéro hardcoding)
- Médiathèque administrable (logos, bannières, photos, images)
- Card Config Schema JSON versionné partagé backend/frontend
- Rendu public piloté par le JSON (carte publique dynamique)
- Compatibilité totale avec le MVP existant (39 tests, Wallet, QR, vCard)

---

## 2. Current Architecture

### Stack
- **Framework :** Laravel 13, PHP 8.4
- **Frontend :** Blade + Tailwind CSS v4, Vite
- **BDD :** MariaDB 11 (Docker), SQLite pour les tests CI
- **Dépendances métier :** `endroid/qr-code:^6.1`, `pkpass/pkpass:^2.5`, `firebase/php-jwt:^7.2`

### Tables existantes
| Table | Rôle |
|---|---|
| `users` | Administrateurs (Laravel standard) |
| `employees` | Collaborateurs : données CSV + données locales mélangées |
| `import_runs` | Historique des imports CSV |
| `audit_logs` | Journal des actions admin |
| `cache`, `jobs`, `sessions` | Laravel standard |

### Problème identifié dans `employees`
La table mélange données CSV (synchronisables) et données locales (à conserver). L'import CSV actuel écrase tous les champs. **La séparation CSV/local est l'une des premières corrections à apporter.**

### Routes existantes (à préserver)
```
GET  /{slug}               → PublicCardController::show
GET  /{slug}/vcard         → PublicCardController::vcard
GET  /{slug}/qr            → PublicCardController::qrPng
GET  /{slug}/photo         → PhotoController::show
GET  /{slug}/apple-wallet  → WalletController::apple
GET  /{slug}/google-wallet → WalletController::google
```

---

## 3. New Data Model — Overview

```
organizations
    └── groups
            └── (default_card_template)
                    └── cards
                            └── employee_card (pivot)
                                    └── employees

media ←─ utilisé par organizations, groups, card_templates, cards (via config JSON)
```

**Nouvelles tables :** `organizations`, `groups`, `media`, `card_templates`, `cards`, `employee_card`

**Modifications `employees` :** ajout `organization_id FK`, `group_id FK`

---

## 4. Organization Model

**Table : `organizations`**

| Colonne | Type | Description |
|---|---|---|
| `id` | bigint PK | |
| `name` | string | Nom de l'organisation |
| `slug` | string unique | URL-safe identifier |
| `description` | text nullable | |
| `primary_color` | string(7) | Défaut `#003189` |
| `secondary_color` | string(7) | Défaut `#0047c8` |
| `text_color` | string(7) | Défaut `#ffffff` |
| `logo_media_id` | FK media nullable | Logo principal |
| `secondary_logo_media_id` | FK media nullable | Logo secondaire |
| `website` | string nullable | |
| `address` | text nullable | |
| `status` | enum(active,inactive) | Défaut `active` |
| `timestamps` | | |

**Relations :**
- `hasMany(Group)`
- `hasMany(Employee)`
- `belongsTo(Media)` (logo)

---

## 5. Group Model

**Table : `groups`**

| Colonne | Type | Description |
|---|---|---|
| `id` | bigint PK | |
| `organization_id` | FK organizations | |
| `name` | string | |
| `slug` | string unique | |
| `description` | text nullable | |
| `logo_media_id` | FK media nullable | |
| `primary_color` | string(7) nullable | Override couleur org |
| `secondary_color` | string(7) nullable | |
| `default_card_template_id` | FK card_templates nullable | |
| `status` | enum(active,inactive) | Défaut `active` |
| `timestamps` | | |

**Relations :**
- `belongsTo(Organization)`
- `hasMany(Employee)`
- `belongsTo(CardTemplate)` (template par défaut)

---

## 6. Card Model

**Table : `cards`**

| Colonne | Type | Description |
|---|---|---|
| `id` | bigint PK | |
| `name` | string | Nom de la carte |
| `slug` | string unique | |
| `organization_id` | FK nullable | Organisation propriétaire |
| `group_id` | FK nullable | Groupe propriétaire |
| `template_id` | FK card_templates nullable | Template d'origine (snapshot) |
| `config` | JSON | CardConfig schema complet |
| `status` | enum(draft,published,disabled) | Défaut `draft` |
| `published_at` | timestamp nullable | |
| `created_by` | FK users | |
| `timestamps` | | |

**Relations :**
- `belongsTo(Organization)`
- `belongsTo(Group)`
- `belongsTo(CardTemplate)`
- `belongsToMany(Employee)` via `employee_card`
- `belongsTo(User)` (created_by)

**Règle critique :** modifier une carte ne modifie jamais le template d'origine.

---

## 7. Template Model

**Table : `card_templates`**

| Colonne | Type | Description |
|---|---|---|
| `id` | bigint PK | |
| `name` | string | |
| `slug` | string unique | |
| `description` | text nullable | |
| `category` | string | moderne / institutionnel / minimal / direction / event |
| `thumbnail_media_id` | FK media nullable | Aperçu du template |
| `config` | JSON | CardConfig complet — sert de base à la copie |
| `is_system` | boolean | Templates livrés avec l'app (`true` = non supprimables) |
| `created_by` | FK users nullable | null pour les templates système |
| `timestamps` | | |

**Clonage :** `Card::createFromTemplate($template)` copie `$template->config` dans la nouvelle carte. Toute modification ultérieure de la carte est indépendante du template.

---

## 8. Media Model

**Table : `media`**

| Colonne | Type | Description |
|---|---|---|
| `id` | bigint PK | |
| `name` | string | Nom affiché |
| `original_name` | string | Nom du fichier original |
| `stored_name` | string | Nom généré côté serveur (UUID + ext) |
| `disk` | string | `local` (volume Docker) |
| `path` | string | Chemin relatif dans le disk |
| `mime_type` | string | Vérifié côté serveur (pas l'extension) |
| `size` | integer | Octets |
| `width` | integer nullable | Pixels (images) |
| `height` | integer nullable | Pixels (images) |
| `category` | enum(logo,banner,photo,image,other) | |
| `uploaded_by` | FK users | |
| `timestamps` | | |

**Règles de sécurité (voir section 17) :** MIME vérifié, nom stocké généré, SVG interdit ou sanitisé, suppression bloquée si utilisé.

---

## 9. Assignment Strategy

**Table : `employee_card` (pivot)**

| Colonne | Type | Description |
|---|---|---|
| `id` | bigint PK | |
| `employee_id` | FK employees UNIQUE | Un employé = une carte max |
| `card_id` | FK cards | |
| `config_overrides` | JSON nullable | Overrides spécifiques à cet employé |
| `assigned_at` | timestamp | |
| `assigned_by` | FK users | |

**Contrainte :** `UNIQUE(employee_id)` — un collaborateur ne peut avoir qu'une carte active à la fois. Pour changer de carte, on met à jour l'enregistrement.

**Assignation en masse :** une carte peut être assignée à tous les membres d'un groupe via `Group::assignCard($card)`.

---

## 10. Override Strategy

La configuration finale d'une carte affichée pour un collaborateur suit cette hiérarchie (du plus spécifique au plus général) :

```
1. config_overrides (employee_card)     ← plus prioritaire
2. card.config                          ← carte assignée
3. group.primary_color / logo           ← groupe
4. organization.primary_color / logo    ← organisation
5. CardConfig defaults (schema)         ← global
```

**Implémentation :** un service `CardRenderService::resolve(Employee $employee): array` calcule la config finale en fusionnant les couches. Le résultat est passé au renderer Blade.

---

## 11. CardConfig Schema

Le schéma est stocké en JSON dans `cards.config` et `card_templates.config`.

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
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "type": "name",
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
        "font_size": 24,
        "font_weight": "700",
        "text_align": "left",
        "border_radius": 0,
        "border_width": 0,
        "border_color": null,
        "background_color": null,
        "padding": [0, 0, 0, 0]
      },
      "data": {
        "format": "full"
      }
    }
  ]
}
```

### Types d'éléments et `data`

| Type | `data` | Rendu |
|---|---|---|
| `text` | `{"content": "Texte libre"}` | Texte statique |
| `name` | `{"format": "full\|first\|last"}` | `employee.first_name` + `last_name` |
| `job_title` | `{}` | `employee.job_title` |
| `department` | `{}` | `employee.department` |
| `email` | `{}` | `employee.email` (lien mailto) |
| `phone` | `{}` | `employee.phone` (lien tel) |
| `linkedin` | `{}` | `employee.linkedin_url` |
| `calendly` | `{}` | `employee.calendly_url` |
| `image` | `{"media_id": 123}` | Image depuis médiathèque |
| `logo` | `{"media_id": 123}` | Logo depuis médiathèque |
| `banner` | `{"media_id": 123}` | Bannière depuis médiathèque |
| `photo` | `{"fallback": "initials\|placeholder"}` | `employee.photo_path` |
| `qr` | `{"target": "public_url"}` | QR vers `employee.publicUrl()` |
| `button` | `{"label": "...", "action": "vcard\|url\|email\|phone", "value": "..."}` | Bouton cliquable |
| `separator` | `{}` | Ligne de séparation |
| `footer` | `{"content": "..."}` | Pied de carte |
| `identity_block` | `{"show_name": true, "show_title": true, "show_dept": true}` | Bloc identité combiné |

---

## 12. Versioning

`schema_version` est un entier dans la racine du JSON.

**Version actuelle :** `1`

**Stratégie de migration :** si une carte enregistrée a `schema_version < current`, un `CardConfigMigrator::migrate($config)` applique les transformations nécessaires au chargement. Chaque migration est une classe versionnée :

```
app/Services/CardConfig/
├── CardConfigMigrator.php   ← dispatch selon schema_version
├── Migrations/
│   └── V1ToV2Migration.php  ← exemple futur
└── CardConfigValidator.php  ← validation de structure
```

---

## 13. Public Rendering

Lors d'une requête `GET /{slug}`, le flux est :

```
PublicCardController::show($slug)
    → Employee::whereSlug($slug)
    → CardRenderService::resolve($employee)  ← calcule config finale
    → view('public.card-dynamic', compact('employee', 'config'))
```

Le renderer Blade itère sur `$config['elements']` et rend chaque type :

```blade
@foreach($config['elements'] as $el)
    @include('public.elements.' . $el['type'], ['el' => $el, 'employee' => $employee])
@endforeach
```

**Fallback :** si un employé n'a pas de carte assignée, la vue actuelle `public/card.blade.php` (hardcodée MMI'e) reste utilisée. Migration progressive possible.

---

## 14. Builder Rendering

**Technologie :** Blade + **Alpine.js** (léger, déjà courant avec Tailwind) + **Moveable.js** (MIT, drag/resize/rotation, ~70kb, sans framework requis).

**Pas de SPA React/Vue.** Le builder est une page Blade enrichie de JavaScript.

```
resources/views/admin/cards/builder.blade.php
resources/js/builder/
├── canvas.js       ← état du canvas, gestion des éléments
├── moveable.js     ← intégration Moveable.js
├── properties.js   ← panneau propriétés (Alpine.js)
├── sidebar.js      ← composants disponibles
└── autosave.js     ← debounce 1500ms → POST /admin/cards/{id}/config
```

L'état du canvas est un objet JS calqué sur le CardConfig schema. À chaque modification, l'objet est sérialisé et envoyé au backend via `fetch`.

---

## 15. Preview

**Endpoint :** `GET /admin/cards/{card}/preview`

Retourne une vue Blade `public.card-dynamic` rendue avec la config de la carte et un employee factice (ou réel si spécifié via `?employee_id=X`).

**Dans le builder :** une `<iframe>` rechargée après chaque `autosave` réussi affiche la preview en temps réel.

```
Builder (Blade/JS)
    → autosave → POST /admin/cards/{id}/config
    → après 200 OK → iframe.src = /admin/cards/{id}/preview?t={timestamp}
```

---

## 16. Publication

**Statuts :** `draft` → `published` → `disabled`

| Statut | Visible publiquement | Modifiable |
|---|---|---|
| `draft` | Non | Oui |
| `published` | Oui | Oui (→ passe en draft temporairement) |
| `disabled` | Non | Oui |

**Règle :** modifier une carte publiée ne doit pas casser la version live immédiatement. Workflow :
1. Admin clique "Modifier" → la carte passe en `draft` (snapshot de la config publiée conservé dans `published_config` optionnel)
2. Admin modifie → autosave
3. Admin clique "Publier" → `status = published`, `published_at = now()`

Pour la Phase 3, on simplifie : pas de `published_config` séparé. Le draft remplace directement. Ajouter un historique de versions en Phase 4 si nécessaire.

---

## 17. Security

### Validation JSON (CardConfig)
- Le JSON reçu en `POST /admin/cards/{id}/config` est validé par `CardConfigValidator`
- Validation de structure : `schema_version`, `canvas`, `elements[]`
- Validation des types d'éléments : whitelist des types autorisés
- Validation des `media_id` : chaque ID doit appartenir à un `Media` existant et accessible
- Limite de taille : payload max 512 KB

### IDOR
- `media_id` dans le JSON → vérifier `Media::findOrFail($id)` — ownership non requis (médiathèque partagée) mais existence obligatoire
- Routes admin protégées par `auth` + `AdminAuth` middleware existant

### Mass Assignment
- `Card`, `Organization`, `Group`, `CardTemplate`, `Media` : `$fillable` explicite sur chaque modèle

### Upload de médias
- MIME détecté avec `finfo` (pas l'extension)
- Formats autorisés : `image/png`, `image/jpeg`, `image/webp`, `image/gif`
- SVG : **interdit par défaut** (risque XSS). Si SVG requis en Phase 4, utiliser `enshrined/svg-sanitize`
- Nom stocké : `Str::uuid() . '.' . $ext` — jamais le nom original
- Dimensions vérifiées avec `getimagesize()`
- Taille max : 5 MB

### CSRF
- Toutes les routes POST/PUT/DELETE admin protégées par le middleware CSRF Laravel existant

### XSS
- `data.content` des éléments `text` et `footer` : échappé via `{{ }}` Blade (pas de `{!! !!}`)

---

## 18. Performance

### Builder
- **Debounce autosave** : 1500ms après dernière modification (pas de save à chaque pixel)
- **Thumbnails médias** : ne pas charger les originaux dans le browser. Générer un thumb 300×300 au upload (GD/Imagick déjà présent)
- **Lazy loading** : médiathèque chargée en `fetch` à l'ouverture du panneau, pas au chargement de la page

### Rendu public
- **Cache HTTP** : `Cache-Control: public, max-age=300` sur `GET /{slug}` pour les cartes publiées
- **QR** : déjà caché 86400s (`QrCodeService`)

---

## 19. Migration Strategy

### Ordre des migrations
```
1. create_media_table
2. create_organizations_table
3. create_groups_table
4. create_card_templates_table
5. create_cards_table
6. create_employee_card_table
7. add_organization_group_to_employees_table  ← nullable FK, pas de données perdues
```

### Séparation CSV / LOCAL dans employees
Le `CsvImportService` actuel écrase tous les champs. Il faut restreindre les champs mis à jour par le CSV :

**Champs CSV (mis à jour par import) :**
`external_key`, `first_name`, `last_name`, `job_title`, `department`, `email`, `phone`, `postal_address`, `website`

**Champs LOCAL (jamais écrasés par import) :**
`organization_id`, `group_id`, `photo_path`, `linkedin_url`, `calendly_url`, `is_active`, `deactivated_at`, `slug`, `qr_token`

**Modification minimale de `CsvImportService` :** définir une constante `CSV_FIELDS` et filtrer `$fillable` dans la méthode `import`.

---

## 20. Test Strategy

### Tests à ajouter

| Test | Type | Vérifie |
|---|---|---|
| `OrganizationTest` | Feature | CRUD, validation, status |
| `GroupTest` | Feature | CRUD, relation org, status |
| `CardTest` | Feature | Création, publication, duplication |
| `CardTemplateTest` | Feature | Clonage non-destructif |
| `MediaTest` | Feature | Upload, MIME validation, no-delete si utilisé |
| `CardConfigValidatorTest` | Unit | JSON schema, media_id, types whitelist |
| `CardRenderServiceTest` | Unit | Hiérarchie override |
| `CsvImportNonDestructiveTest` | Feature | CSV update → org/group/photo inchangés |
| `AssignmentTest` | Feature | Unique constraint employee_card |
| `PublicCardDynamicTest` | Feature | GET /{slug} → 200 avec config |
| `UrlQrStabilityTest` | Feature | Organisation/groupe changé → slug/qr_token identiques |

### Règle CI
Les 39 tests existants doivent continuer à passer. Aucune migration ne doit casser la DB de test SQLite.

---

## 21. Gemini Integration Contract

### Ce que Claude fournit à Gemini
- Le CardConfig schema complet (ce document, section 11)
- Toutes les routes backend (fichier `GEMINI_HANDOFF.md`)
- Les contrats de payload (save, preview, publish)
- Les endpoints de la médiathèque
- Un prompt précis par chantier (dossier `docs/prompts/`)

### Ce que Gemini construit
- L'interface du Card Builder (canvas + sidebar + panel propriétés)
- Les pages CRUD organisations, groupes
- La médiathèque (upload + browse + sélection)
- La galerie de templates
- La page de preview
- Le layout admin étendu (navigation mise à jour)

### Ce que Gemini NE modifie PAS
- Tout le code MVP existant (voir `GEMINI_HANDOFF.md` section FILES NOT TO MODIFY)
- Les routes publiques `/{slug}/*`
- Les services QR, vCard, Wallet

### Format d'échange
Gemini retourne ses livrables avec le header :
```
FEATURE: <ID>
STATUS: done|partial|blocked
FILES CHANGED: [liste]
BACKEND ASSUMPTIONS: [liste]
BLOCKERS: [liste]
QUESTIONS FOR CLAUDE: [liste]
```
