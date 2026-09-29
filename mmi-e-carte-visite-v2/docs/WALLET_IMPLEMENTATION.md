# WALLET_IMPLEMENTATION — Phase 2

## Architecture

```
app/Services/Wallet/
├── Contracts/
│   └── WalletProviderInterface.php   ← contrat commun Apple + Google
├── Apple/
│   ├── AppleWalletProvider.php       ← orchestre pkpass/pkpass
│   └── PassBuilder.php               ← construit pass.json (pur données, testable)
├── Google/
│   ├── GoogleWalletProvider.php      ← orchestre firebase/php-jwt
│   └── ObjectBuilder.php             ← construit le Generic Object (pur données, testable)
└── WalletManager.php                 ← façade injectée partout

app/Http/Controllers/WalletController.php
routes/web.php                        ← GET /{slug}/apple-wallet, GET /{slug}/google-wallet
config/wallet.php
```

**Règle fondamentale :** Wallet est une couche indépendante. `PublicCardController`, `Employee` et les autres modules MVP ne sont pas modifiés. Les boutons sont injectés via un View Composer (AppServiceProvider).

---

## Apple Wallet

### Prérequis

1. **Apple Developer Program** — compte payant (~99 $/an)
2. **Pass Type ID** — créer sur https://developer.apple.com → Certificates, Identifiers & Profiles → Identifiers → Pass Type IDs
   - Exemple : `pass.fr.mmi-e.carte-visite`
3. **Certificat** — générer un Certificate Signing Request (CSR) avec Keychain Access, soumettre sur Apple Developer, exporter en `.p12`
4. **WWDR** — télécharger Apple Worldwide Developer Relations Certificate Authority depuis https://www.apple.com/certificateauthority/ (fichier `.pem` ou `.cer` à convertir)

### Placement des fichiers

```
storage/app/private/wallet/apple/   ← JAMAIS commité dans Git
├── certificate.p12
├── wwdr.pem
├── logo.png       (160 × 50 px)
├── logo@2x.png    (320 × 100 px)
├── icon.png       (29 × 29 px)
└── icon@2x.png    (58 × 58 px)
```

Si les images ne sont pas présentes, un placeholder bleu MMI'e est généré automatiquement (à remplacer en production).

### Variables d'environnement

```env
APPLE_TEAM_ID=ABCD1234EF
APPLE_PASS_TYPE_IDENTIFIER=pass.fr.mmi-e.carte-visite
APPLE_CERTIFICATE_PATH=/var/www/storage/app/private/wallet/apple/certificate.p12
APPLE_CERTIFICATE_PASSWORD=mot_de_passe_du_p12
APPLE_WWDR_PATH=/var/www/storage/app/private/wallet/apple/wwdr.pem
APPLE_LOGO_PATH=/var/www/storage/app/private/wallet/apple/logo.png
APPLE_LOGO2X_PATH=/var/www/storage/app/private/wallet/apple/logo@2x.png
APPLE_ICON_PATH=/var/www/storage/app/private/wallet/apple/icon.png
APPLE_ICON2X_PATH=/var/www/storage/app/private/wallet/apple/icon@2x.png
```

### Identité stable du pass

| Identifiant Apple | Valeur |
|---|---|
| `serialNumber` | `employee.qr_token` (UUID, jamais modifié) |
| `passTypeIdentifier` | `APPLE_PASS_TYPE_IDENTIFIER` (config) |
| `teamIdentifier` | `APPLE_TEAM_ID` (config) |

La modification du téléphone, de l'email ou du poste n'impacte jamais ces identifiants.

### Distribution

`GET /{slug}/apple-wallet` → Content-Type: `application/vnd.apple.pkpass` → iOS/macOS propose d'ajouter à Wallet.

### Mise à jour des passes (Phase 3)

Pour pousser des mises à jour sans que l'utilisateur retélécharge, il faudra :
1. Ajouter `webServiceURL` et `authenticationToken` dans pass.json
2. Implémenter les endpoints Apple Push Update (GET/PUT `/v1/passes/...`, GET `/v1/devices/...`)
3. Utiliser APNs pour notifier les passes à mettre à jour

---

## Google Wallet

### Prérequis

1. **Google Wallet API Issuer Account** — https://pay.google.com/business/console
   - Départ en **Demo Mode** (passes visibles uniquement par les testeurs autorisés)
   - Demander le **Publishing Access** après validation Google (formulaire + prérequis)
2. **Google Cloud Project** — activer l'API Google Wallet
3. **Service Account** — créer dans IAM, rôle `Wallet Object Issuer`, exporter la clé JSON

### Placement des fichiers

```
storage/app/private/wallet/google/   ← JAMAIS commité dans Git
└── service-account.json
```

### Variables d'environnement

```env
GOOGLE_WALLET_ISSUER_ID=3388000000022
GOOGLE_WALLET_CLASS_SUFFIX=mmie-carte-visite
GOOGLE_WALLET_SA_KEY_PATH=/var/www/storage/app/private/wallet/google/service-account.json
GOOGLE_WALLET_LOGO_URL=https://carte.mmi-e.fr/images/logo-google-wallet.png
```

### Generic Class

La Generic Class définit le gabarit commun à tous les passes MMI'e. Elle est créée **une fois** dans la console Google Wallet (ou via l'API) avec l'ID :
```
{ISSUER_ID}.{CLASS_SUFFIX}
```
exemple : `3388000000022.mmie-carte-visite`

Si la classe n'existe pas encore, la première tentative d'ajout renverra une erreur Google. Elle doit être créée au préalable.

### Generic Object

Chaque collaborateur a un objet unique :
```
{ISSUER_ID}.employee-{employee.id}
```
exemple : `3388000000022.employee-42`

L'ID est dérivé de `employee.id` (clé primaire, jamais modifiée). La modification des données ne change pas l'identifiant.

### Flux JWT

```
1. ObjectBuilder construit le Generic Object (payload PHP)
2. GoogleWalletProvider signe le payload avec la clé RS256 du Service Account
3. JWT retourné dans l'URL : https://pay.google.com/gp/v/save/{jwt}
4. WalletController redirige vers cette URL
5. Le navigateur (Android) ouvre Google Wallet pour ajouter le pass
```

La clé privée du Service Account ne quitte **jamais** le serveur.

### Synchronisation des mises à jour (Phase 3)

Pour mettre à jour automatiquement un Generic Object quand un collaborateur est modifié :
1. Ajouter un Observer sur le modèle `Employee`
2. L'Observer appelle `GoogleWalletProvider::updateObject($employee)` via l'API REST Google Wallet
3. URL : `PATCH https://walletobjects.googleapis.com/walletobjects/v1/genericObject/{objectId}`
4. Authentification via OAuth2 avec le Service Account

L'architecture actuelle (ObjectBuilder + GoogleWalletProvider) est conçue pour cette extension sans refonte.

---

## Tests

Les tests CI (GitHub Actions) n'utilisent jamais de vrais credentials :

| Test | Stratégie |
|---|---|
| Structure du pass Apple | `PassBuilder` pur PHP, pas de certificat |
| Structure de l'objet Google | `ObjectBuilder` pur PHP, pas de JWT |
| Identifiants stables | Tests unitaires sur `serialNumber` / `object.id` |
| Endpoints HTTP non configurés | `Config::set()` pour forcer credentials vides → 503 |
| Carte inactive | Requête → 404 |
| Pas de secret dans les réponses | `assertStringNotContainsString('private_key', ...)` |

---

## Rotation des credentials

### Apple
1. Générer un nouveau certificat sur Apple Developer
2. Remplacer `certificate.p12` sur le serveur
3. Mettre à jour `APPLE_CERTIFICATE_PASSWORD` si différent
4. Redémarrer PHP-FPM (`docker compose restart app`)
5. Vérifier un téléchargement de pass en production

### Google
1. Créer une nouvelle clé dans Google Cloud → IAM → Service Accounts → Keys
2. Remplacer `service-account.json` sur le serveur
3. Supprimer l'ancienne clé dans la console Google Cloud
4. Redémarrer PHP-FPM

---

## Ce qui ne change pas

| Élément | Statut |
|---|---|
| `employee.slug` | Jamais modifié par Wallet |
| `employee.qr_token` | Jamais modifié par Wallet |
| `employee.publicUrl()` | Jamais modifiée par Wallet |
| QR code MVP | Inchangé |
| vCard | Inchangée |
| Import CSV | Inchangé |
| Administration | Inchangée |
| Tests existants (39/39) | Non régressés |
