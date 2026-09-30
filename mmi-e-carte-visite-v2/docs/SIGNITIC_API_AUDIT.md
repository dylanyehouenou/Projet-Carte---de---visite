# SIGNITIC_API_AUDIT — Phase 3 Pré-étude

> **Date :** 2026-09-29
> **Auteur :** Audit automatique (Claude Code)
> **Source officielle :** https://developers.signitic.app/llms-full.txt

---

## ⚠️ Note sur la clé API fournie

La clé `8957a5102d5c7267cd7d7477edf943e6` a retourné `{"success":false,"code":99,"message":"Invalid API key."}` sur tous les endpoints testés. **Aucun appel live n'a pu être effectué.** L'intégralité de ce document s'appuie sur la documentation officielle publique récupérée le 2026-09-29. Les schémas de réponse sont ceux fournis dans la doc, non vérifiés sur données réelles MMI'e.

**Action requise avant Phase 3 :** Régénérer la clé depuis https://app.signitic.com → Paramètres → API.

---

## 1. Coordonnées de l'API

| Paramètre | Valeur |
|---|---|
| Base URL | `https://api.signitic.app/` |
| Version dans l'URL | **Aucune** — pas de `/v1/` ni de préfixe |
| Authentification | Header `x-api-key: <votre_clé>` |
| Format des réponses | JSON (sauf `GET /signatures/:email/html`) |
| Limite payload | 3 MB pour POST/DELETE /users |

**Erreur fréquente à éviter :** utiliser `Authorization: Bearer` ou ajouter `/v1/` dans l'URL → les deux causent des 403/401.

---

## 2. Carte complète des endpoints

### Core API

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/users` | Liste tous les utilisateurs (email + statut) |
| `GET` | `/users/:email` | Détail complet d'un utilisateur |
| `POST` | `/users` | Crée ou met à jour des utilisateurs (batch) |
| `DELETE` | `/users` | Supprime des utilisateurs (différé 1 mois) |
| `GET` | `/user-change-requests` | Liste toutes les demandes de modification en attente |
| `GET` | `/user-change-requests/:email` | Demandes en attente pour un utilisateur |
| `PATCH` | `/user-change-requests/:email/:field` | Approuve une demande |
| `DELETE` | `/user-change-requests/:email/:field` | Rejette une demande |
| `GET` | `/signatures/:email/json` | Signature HTML dans envelope JSON |
| `GET` | `/signatures/:email/html` | Signature HTML brute (Content-Type: text/html) |
| `GET` | `/desktop/:email` | Config agent desktop (RDS/GPO) |

### Reseller API (hors scope MMI'e)

| Méthode | Endpoint | Description |
|---|---|---|
| `POST` | `/reseller/workspace` | Crée un workspace client |
| `GET` | `/reseller/consumption` | Consommation de licences par mois |

### MCP Server (beta, non disponible publiquement)

- Endpoint : `https://mcp.signitic.com/mcp`
- Auth : même `x-api-key`
- Transport : HTTP uniquement (pas de stdio)
- Accès : non ouvert au public en date du 2026-09-29
- Voir section 10 pour le catalogue d'outils MCP

---

## 3. Schéma utilisateur — GET /users (liste)

Réponse succès :
```json
{
  "success": true,
  "users": [
    {
      "email": "user1@signitic.fr",
      "enabled": 1
    }
  ]
}
```

**Note critique :** `enabled` est un entier API (`1` = actif, `2` = inactif), différent du booléen `enabled` dans le détail.

---

## 4. Schéma utilisateur — GET /users/:email (détail)

```json
{
  "success": true,
  "code": 200,
  "data": {
    "id": 1234567,
    "email": "user1@signitic.fr",
    "firstname": "Johanna",
    "lastname": "Doe",
    "enabled": true,
    "group": "Group",
    "picture": null,
    "phone": "+33 1 34 33 22 33",
    "mobile": "+33 6 34 33 22 33",
    "title": "CEO",
    "unit": "Department",
    "address": "1600 Pennsylvania Avenue NW<br/>Washington, DC 20500,<br/>USA",
    "postal_code": "75008",
    "city": "Paris",
    "formula": "Kind regards,<br/>John",
    "vcard_url": "https://signitic.cards/placeholder/johanna.doe",
    "calendar_link": "calendar.com",
    "github_link": "github.com",
    "twitter_link": "twitter.com",
    "facebook_link": "facebook.com",
    "linkedin_link": "linkedin.com",
    "instagram_link": "instagram.com",
    "xing_link": "xing.com",
    "messenger_link": null,
    "threads_link": "threads.net",
    "strava_link": "strava.com",
    "whatsapp_link": "whatsapp.com",
    "pending_change_requests": [],
    "extra_1": "extra field 1",
    "extra_2": "...",
    "extra_10": "extra field 10",
    "extra_20": "extra field 20",
    "path": {
      "parent_entity": "Company",
      "entity": "Washington Branch",
      "group": "Marketing Direction"
    }
  }
}
```

**Champs notables pour la synchro MMI'e :**

| Champ Signitic | Champ Employee Laravel | Comportement |
|---|---|---|
| `email` | `email` | **Clé de jointure** |
| `firstname` | `first_name` | Lecture/écriture |
| `lastname` | `last_name` | Lecture/écriture |
| `title` | `job_title` | Lecture/écriture |
| `phone` | `phone` | Lecture/écriture |
| `mobile` | — | Non géré côté MMI'e |
| `linkedin_link` | `linkedin_url` | Lecture/écriture |
| `enabled` (bool) | `is_active` | Lecture/écriture |
| `group` | — | Non géré (structure Signitic) |
| `extra_1`…`extra_20` | — | Disponible pour extensions |

---

## 5. POST /users — Mise à jour batch

La même route POST crée ET met à jour (upsert par email). Partial update supporté : seuls les champs envoyés sont modifiés.

```json
{
  "users": [
    {
      "email": "alice.martin@mmi-e.fr",
      "firstname": "Alice",
      "lastname": "Martin",
      "title": "Responsable Pédagogique",
      "phone": "+33 6 00 00 00 01",
      "linkedin_link": "https://linkedin.com/in/alice-martin",
      "enabled": true,
      "path": {
        "group": "MMI'e"
      }
    }
  ]
}
```

**Contraintes :**
- Payload JSON brut limité à 3 MB
- Pas de pagination côté requête (envoi d'un seul batch)
- Suppression = `DELETE /users` → différé 1 mois (soft delete)

---

## 6. Gestion des erreurs

| Code HTTP | Code interne | Signification |
|---|---|---|
| 401 | 99 | Clé API absente ou invalide |
| 400 | 101 | Format email invalide |
| 404 | 102 | Email inconnu dans le workspace |
| 400 | 103 | Payload JSON invalide |
| 422 | 104 | Payload > 3 MB |
| 405 | 100 | Segment réservé `users` utilisé comme email |
| 422 | 106 | Utilisateur inactif — signature impossible |

**Legacy payload :** certains endpoints (ex. `/signatures/:email`) retournent `{"error":"..."}` au lieu de `{"success":false}` pour les erreurs 401 liées à une clé incompatible. Le client doit tester les deux clés `success` et `error`.

---

## 7. Schéma signature — GET /signatures/:email/json

```json
{
  "success": true,
  "html": "<body>...</body>"
}
```

- `mode=json` → JSON avec `html`
- `mode=html` → HTML brut, Content-Type `text/html`
- Erreur 422/code 106 si l'utilisateur est inactif

---

## 8. Comportements importants

1. **Suppression différée :** `DELETE /users` ne supprime pas immédiatement — l'utilisateur est marqué pour suppression dans 1 mois. Les GET `/users` verront toujours l'utilisateur pendant ce délai.

2. **Upsert par email :** `POST /users` fait un upsert. Si l'email existe déjà, l'utilisateur est mis à jour. Idempotent.

3. **Partial update :** Seuls les champs envoyés en `POST /users` sont modifiés. Envoyer `{"email":"x","title":"Y"}` ne touche pas `phone`, `firstname`, etc.

4. **Path dispatch :** L'arborescence de groupes est résolue top-down (`parent_entity` → `entity` → `group`). Un groupe inexistant est créé automatiquement. Attention à ne pas créer de groupes parasites en production.

5. **Enabled API vs booléen :** La liste `/users` renvoie `"enabled": 1` (int), le détail `/users/:email` renvoie `"enabled": true` (bool). Ne pas confondre dans le mapping.

6. **Liste limitée à 25 dans MCP :** Uniquement pour le serveur MCP (non public). L'API REST `/users` renvoie tous les utilisateurs sans pagination documentée.

---

## 9. Webhooks entrants (Signitic → MMI'e)

Signitic peut envoyer un webhook lors d'une soumission de formulaire contact vCard :

```json
{
  "contact": {
    "firstname": "Jean",
    "lastname": "Dupont",
    "email": "jean.dupont@example.com",
    "phone": null,
    "company": null
  },
  "date": "2025-03-31T11:18:20+00:00",
  "employee": {
    "email": "alice.martin@mmi-e.fr",
    "fullName": "Alice Martin"
  }
}
```

- Header inclus : `x-api-key` (à vérifier côté serveur pour authentification)
- URL configurable dans les paramètres vCard de Signitic

**Non pertinent pour la synchro Phase 3** (c'est du inbound contact, pas du user sync).

---

## 10. MCP Server (beta, non public)

> Documenté pour référence future uniquement. Non disponible au 2026-09-29.

- URL : `https://mcp.signitic.com/mcp`
- Auth : `x-api-key`
- Session TTL : 1 heure
- Rate limit : 30 appels/minute/outil/workspace
- Tous les outils sont **read-only**

### Catalogue d'outils MCP

| Domaine | Outils |
|---|---|
| `user` | `user_list`, `user_count`, `user_find`, `user_get_summary`, `user_get_support_context` |
| `campaign` | `campaign_list`, `campaign_find`, `campaign_get_summary`, `campaign_get_support_context` |
| `group` | `group_list`, `group_find`, `group_get_summary`, `group_get_support_context` |
| `workspace` | `workspace_get_user_profile_completeness`, `workspace_get_campaign_assignment_overview`, `workspace_get_group_completeness` |
| `stats` | `stats_get_workspace_overview`, `stats_get_campaign_performance`, `stats_get_user_metrics` |

**Identifiants MCP :**
- Utilisateur : `usr_<26 chars base32>`
- Campagne : `cp_<26 chars base32>`
- Groupe : `grp_<26 chars base32>`
- Workspace : `<26 chars base32>`

---

## 11. Architecture de synchronisation proposée — Phase 3

### Stratégie recommandée : Push depuis MMI'e → Signitic

**Déclencheur :** Observer Laravel sur le modèle `Employee` (événements `created`, `updated`, `deleted`).

```
Employee::saved  →  SigniticSyncService::upsert($employee)   →  POST /users
Employee::deleted →  SigniticSyncService::deactivate($employee) → POST /users [enabled:false]
```

**Pourquoi pas de cron ?**
- L'Observer garantit la synchro immédiate sans délai batch
- Réduit les appels API inutiles (seuls les employés modifiés sont envoyés)
- Cohérent avec le principe "Wallet est une couche indépendante"

### Mapping de champs

```php
// SigniticSyncService::buildPayload(Employee $employee): array
return [
    'email'         => $employee->email,
    'firstname'     => $employee->first_name,
    'lastname'      => $employee->last_name,
    'title'         => $employee->job_title ?? '',
    'phone'         => $employee->phone ?? '',
    'linkedin_link' => $employee->linkedin_url ?? '',
    'enabled'       => $employee->is_active,
    'path'          => ['group' => config('signitic.default_group', "MMI'e")],
];
```

### Structure de fichiers proposée

```
app/Services/Signitic/
├── SigniticClient.php          ← HTTP client (Guzzle/Http facade)
├── SigniticSyncService.php     ← logique upsert/deactivate
└── EmployeeSyncObserver.php    ← Observer Employee

config/signitic.php             ← SIGNITIC_API_KEY, default_group
```

### Variables d'environnement à ajouter

```env
SIGNITIC_API_KEY=               # Clé API depuis app.signitic.com
SIGNITIC_DEFAULT_GROUP=MMI'e    # Groupe Signitic cible
SIGNITIC_ENABLED=false          # Feature flag — false = synchro désactivée
```

### Gestion des erreurs et retry

- HTTP 401 → log critique, ne pas retenter (clé invalide)
- HTTP 422/503 → log warning, retenter via `dispatch()->delay(now()->addMinutes(5))`
- HTTP 404 → ne peut pas arriver sur POST upsert
- Utiliser `try/catch` autour de l'appel HTTP pour ne jamais bloquer le save Employee

### Idempotence

`POST /users` est idempotent par email. Envoyer plusieurs fois le même payload est sans effet négatif.

### Test CI

```php
// Tests\Feature\SigniticSyncTest.php
Http::fake(['https://api.signitic.app/users' => Http::response(['success'=>true,'code'=>200,'value'=>['added'=>1,'edited'=>0]], 200)]);
$emp = Employee::create([...]);
Http::assertSent(fn($r) => $r->url() === 'https://api.signitic.app/users' && $r['x-api-key'] === 'test-key');
```

---

## 12. Ce qui ne doit pas changer (garanties MVP)

| Élément | Statut |
|---|---|
| `employee.slug` | Jamais modifié par Signitic |
| `employee.qr_token` | Jamais modifié par Signitic |
| PublicCardController | Non touché |
| WalletController | Non touché |
| Tests existants | Non régressés |

La synchro Signitic est une couche indépendante, exactement comme la couche Wallet.

---

## 13. Points bloquants avant Phase 3

| Blocant | Action |
|---|---|
| Clé API invalide | Régénérer depuis https://app.signitic.com |
| Workspace Signitic non configuré | Créer un workspace + vérifier que les utilisateurs existent |
| Mapping `group` inconnu | Vérifier le nom exact du groupe dans la console Signitic |
| MCP non public | Ne pas planifier d'utilisation MCP avant ouverture officielle |

---

```
SIGNITIC_API_STATUS:
  documentation_fetched: true
  source: https://developers.signitic.app/llms-full.txt
  live_calls_attempted: true
  live_calls_succeeded: false
  api_key_status: INVALID (code 99 — "Invalid API key.")
  endpoints_mapped: 11 (Core API) + 2 (Reseller) + 15 (MCP tools, non public)
  sync_architecture_proposed: true
  implementation_done: false
  blockers:
    - API key must be regenerated from https://app.signitic.com
    - Live schema verification pending (field names confirmed from official docs)
  next_step: SIGNITIC-002 — Implementation after key validation
```
