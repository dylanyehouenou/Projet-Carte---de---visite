# WALLET

## Apple Wallet

Apple documente les passes `.pkpass`, leur signature et leur distribution depuis une page web.

Le projet doit encapsuler Apple Wallet dans un provider séparé afin que le MVP fonctionne sans credentials Apple.

Les certificats et clés restent hors Git.

## Google Wallet

Google Wallet repose notamment sur un Pass Class et des Pass Objects et peut utiliser un JWT pour l'émission d'un pass.

Les credentials Google restent hors Git.

## Architecture

Créer une abstraction de type :

WalletService

avec :

- AppleWalletProvider
- GoogleWalletProvider

Ainsi le cœur du produit reste indépendant de Wallet.

## Mise à jour

La logique Wallet doit être pensée séparément de la stabilité de la carte publique, du QR et de l’URL.
