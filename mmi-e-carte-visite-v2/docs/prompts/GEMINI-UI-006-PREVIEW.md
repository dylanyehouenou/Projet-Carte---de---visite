# GEMINI-UI-006 — Preview de Carte

---

## FEATURE ID
`GEMINI-UI-006`

---

## OBJECTIVE
Construire le renderer de preview d'une carte : une vue Blade qui interprète un CardConfig JSON et rend la carte visuellement. Ce renderer est utilisé à la fois dans le builder (iframe temps réel) et comme page de preview standalone.

---

## CONTEXT
Stack : Laravel 13 / Tailwind CSS v4. La preview est une vue Blade **sans layout admin** (pas de navbar, pas de footer), rendue dans une iframe dans le builder ou accessible directement via URL.

Le renderer doit itérer sur les `elements` du CardConfig et rendre chaque type selon son style et ses données. Les données dynamiques (nom, poste, email…) sont injectées depuis un `Employee` réel ou un employé factice de démonstration.

---

## WHAT EXISTS
- CardConfig schema v1 (voir `CARD_BUILDER_ARCHITECTURE.md` section 11)
- Route `GET /admin/cards/{card}/preview` → retourne HTML (vue standalone)
- Route `GET /admin/cards/{card}/preview?employee_id=X` → preview avec employé réel
- Layout public : `resources/views/layouts/app.blade.php`
- Police : Plus Jakarta Sans, couleurs #003189 / #0047c8

---

## WHAT GEMINI MUST BUILD

### 1. `resources/views/admin/cards/preview.blade.php`
Vue sans layout admin (HTML complet autonome, ou utilise `layouts/app.blade.php`).

- Rendu du canvas (div width/height du config, background selon type)
- Itération sur `elements` par z-index croissant
- Chaque élément est rendu par un sous-composant selon son `type`
- L'ensemble est centré dans la page (pour l'iframe dans le builder)

### 2. `resources/views/public/card-elements/` (dossier de partials)

Un partial par type d'élément :

```
resources/views/public/card-elements/
├── _text.blade.php
├── _name.blade.php
├── _job_title.blade.php
├── _department.blade.php
├── _email.blade.php
├── _phone.blade.php
├── _linkedin.blade.php
├── _calendly.blade.php
├── _image.blade.php
├── _logo.blade.php
├── _banner.blade.php
├── _photo.blade.php
├── _qr.blade.php
├── _button.blade.php
├── _separator.blade.php
├── _footer.blade.php
└── _identity_block.blade.php
```

Chaque partial reçoit `$el` (l'élément CardConfig) et `$employee` (l'Employee ou un objet factice).

### 3. Logique de positionnement

Chaque élément est positionné en `absolute` dans le canvas :
```html
<div
  style="
    position: absolute;
    left: {{ $el['x'] }}px;
    top: {{ $el['y'] }}px;
    width: {{ $el['width'] }}px;
    height: {{ $el['height'] }}px;
    transform: rotate({{ $el['rotation'] }}deg);
    opacity: {{ $el['opacity'] }};
    z-index: {{ $el['z_index'] }};
    {{ $el['hidden'] ? 'display:none;' : '' }}
    font-family: {{ $el['style']['font_family'] ?? 'Plus Jakarta Sans' }};
    font-size: {{ $el['style']['font_size'] ?? 16 }}px;
    font-weight: {{ $el['style']['font_weight'] ?? '400' }};
    color: {{ $el['style']['color'] ?? '#111827' }};
    text-align: {{ $el['style']['text_align'] ?? 'left' }};
    background-color: {{ $el['style']['background_color'] ?? 'transparent' }};
    border-radius: {{ $el['style']['border_radius'] ?? 0 }}px;
  "
>
  @include('public.card-elements._' . $el['type'], ['el' => $el, 'employee' => $employee])
</div>
```

---

## DATA CONTRACT

### Vue reçoit depuis le contrôleur
```php
[
  'card'     => Card $card,
  'config'   => array $config,   // CardConfig décodé
  'employee' => Employee $employee,  // réel ou factice
]
```

### Employé factice (si pas de `?employee_id`)
```php
$fakeEmployee = new Employee([
  'first_name'   => 'Alice',
  'last_name'    => 'Martin',
  'job_title'    => 'Responsable Pédagogique',
  'department'   => 'Direction',
  'email'        => 'alice.martin@mmi-e.fr',
  'phone'        => '+33 6 00 00 00 01',
  'linkedin_url' => 'https://linkedin.com/in/alice-martin',
  'calendly_url' => null,
  'slug'         => 'alice-martin',
  'qr_token'     => 'demo-token',
  'is_active'    => true,
]);
```

---

## BACKEND CONTRACT

```
GET /admin/cards/{card}/preview
GET /admin/cards/{card}/preview?employee_id=42
GET /admin/cards/{card}/preview?t={timestamp}  ← force rechargement iframe

Response: HTML complet (status 200)
```

---

## RENDERING RULES PAR TYPE

| Type | Rendu |
|---|---|
| `text` | `<p>{{ $el['data']['content'] }}</p>` |
| `name` | `{{ $employee->first_name }} {{ strtoupper($employee->last_name) }}` selon `format` |
| `job_title` | `{{ $employee->job_title }}` (masqué si vide) |
| `department` | `{{ $employee->department }}` (masqué si vide) |
| `email` | `<a href="mailto:...">{{ $employee->email }}</a>` (masqué si vide) |
| `phone` | `<a href="tel:...">{{ $employee->phone }}</a>` (masqué si vide) |
| `linkedin` | `<a href="{{ $employee->linkedin_url }}">LinkedIn</a>` (masqué si vide) |
| `calendly` | `<a href="{{ $employee->calendly_url }}">Rendez-vous</a>` (masqué si vide) |
| `image`, `logo`, `banner` | `<img src="{{ route('admin.media.show', $el['data']['media_id']) }}" alt="">` |
| `photo` | `<img src="{{ route('card.photo', $employee->slug) }}">` ou initiales si fallback |
| `qr` | `<img src="{{ route('card.qr', $employee->slug) }}">` |
| `button` | `<a href="...">{{ $el['data']['label'] }}</a>` selon `action` |
| `separator` | `<hr>` stylé |
| `footer` | `<p>{{ $el['data']['content'] }}</p>` |
| `identity_block` | Bloc combiné nom + titre + service selon flags `show_*` |

**Sécurité :** tous les textes dynamiques sont échappés avec `{{ }}` (jamais `{!! !!}`).

### Background du canvas
```blade
@if($config['canvas']['background']['type'] === 'color')
  style="background-color: {{ $config['canvas']['background']['value'] }}"
@elseif($config['canvas']['background']['type'] === 'gradient')
  style="background: linear-gradient(to bottom right, {{ $bg['gradient']['from'] }}, {{ $bg['gradient']['to'] }})"
@elseif($config['canvas']['background']['type'] === 'image')
  style="background-image: url('{{ route('admin.media.show', $bg['media_id']) }}'); background-size: cover;"
@endif
```

---

## COMPONENTS

Pas de composants Alpine.js pour la preview — rendu Blade pur.

---

## INTERACTIONS

La preview est statique (lecture seule). Aucune interaction attendue côté preview.

---

## RESPONSIVE

La preview rend le canvas à taille fixe (390×844px) centré dans la page. L'iframe dans le builder gère le scaling.

---

## ACCESSIBILITY

- `<img>` médias : `alt="{{ $el['type'] }}"` (minimal, améliorer en Phase 4)
- Page preview standalone : `<html lang="fr">`

---

## FILES TO CREATE

```
resources/views/admin/cards/preview.blade.php
resources/views/public/card-elements/_text.blade.php
resources/views/public/card-elements/_name.blade.php
resources/views/public/card-elements/_job_title.blade.php
resources/views/public/card-elements/_department.blade.php
resources/views/public/card-elements/_email.blade.php
resources/views/public/card-elements/_phone.blade.php
resources/views/public/card-elements/_linkedin.blade.php
resources/views/public/card-elements/_calendly.blade.php
resources/views/public/card-elements/_image.blade.php
resources/views/public/card-elements/_logo.blade.php
resources/views/public/card-elements/_banner.blade.php
resources/views/public/card-elements/_photo.blade.php
resources/views/public/card-elements/_qr.blade.php
resources/views/public/card-elements/_button.blade.php
resources/views/public/card-elements/_separator.blade.php
resources/views/public/card-elements/_footer.blade.php
resources/views/public/card-elements/_identity_block.blade.php
```

---

## DO NOT MODIFY

```
resources/views/public/card.blade.php       ← carte hardcodée existante, pas touchée
resources/views/public/disabled.blade.php
app/Http/Controllers/PublicCardController.php
(tous les fichiers MVP listés dans GEMINI_HANDOFF.md)
```

---

## ACCEPTANCE CRITERIA

- [ ] `GET /admin/cards/{card}/preview` retourne un HTML 200
- [ ] Le canvas est rendu à 390×844px avec le background configuré
- [ ] Les éléments de type `name`, `job_title`, `email`, `qr` sont affichés correctement
- [ ] L'employé factice est utilisé si `?employee_id` est absent
- [ ] L'employé réel est utilisé si `?employee_id=42` est passé
- [ ] Les champs vides de l'employé sont masqués (pas de texte vide visible)
- [ ] Tous les textes sont échappés (pas de XSS)
- [ ] L'iframe dans le builder se recharge correctement avec `?t={timestamp}`

---

## HANDOFF BACK TO CLAUDE

```
FEATURE: GEMINI-UI-006
STATUS: done|partial|blocked
FILES CHANGED: [liste]
UI COMPONENTS: [liste]
DATA USED: [CardConfig schema v1, routes utilisées]
BACKEND ASSUMPTIONS: [suppositions]
BLOCKERS: [si applicable]
QUESTIONS FOR CLAUDE: [questions]
TESTING: [tests écrits]
NEXT ACTION: Retour à Claude pour intégration et tests backend
```
