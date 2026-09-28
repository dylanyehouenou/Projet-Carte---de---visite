# MASTER — CAHIER DES CHARGES

## 1. Contexte

La MMI’e souhaite mettre à disposition de ses collaborateurs une solution de carte de visite virtuelle professionnelle.

## 2. Objectifs

La solution doit permettre :

- une carte numérique individuelle ;
- le partage simple depuis un smartphone ;
- l’enregistrement des coordonnées dans les contacts ;
- Apple Wallet et Google Wallet ;
- un QR code individuel ;
- l’étude du partage par proximité ;
- le respect de la charte graphique MMI’e ;
- la mise à jour centralisée ;
- un accès direct à Calendly ou autre outil de rendez-vous lorsque disponible.

## 3. Données

Champs minimum :

- prénom ;
- nom ;
- fonction ;
- service ;
- adresse mail ;
- numéro de téléphone ;
- adresse postale ;
- site internet MMI’e ;
- photo facultative ;
- LinkedIn facultatif ;
- Calendly facultatif ;
- QR code.

Une information absente ne doit pas produire de bouton vide.

## 4. Carte publique

URL cible :

`carte.mmi-e.fr/prenom-nom`

La page présente :

- Prénom NOM ;
- Fonction ;
- Service ;
- logo MMI’e ;
- site internet ;
- téléphone ;
- mail ;
- photo si disponible.

Actions conditionnelles :

- Ajouter à mes contacts ;
- Appeler ;
- Envoyer un e-mail ;
- Prendre rendez-vous ;
- LinkedIn ;
- Wallet selon disponibilité.

## 5. QR code

Le QR encode uniquement l’URL permanente de la carte.

Le QR doit :

- être affichable depuis le smartphone ;
- être intégré à la carte ;
- être téléchargeable en image ;
- pouvoir être utilisé sur signature mail, tour de cou, présentation, flyer, etc.

Une modification d’une donnée ne doit jamais imposer un nouveau QR.

## 6. Import Signitic

L’administrateur importe un CSV.

Le système doit :

- identifier les collaborateurs existants ;
- mettre à jour les cartes ;
- créer les nouveaux ;
- signaler autant que possible les personnes absentes du nouveau fichier ;
- ne pas supprimer automatiquement les absents.

## 7. Administration

L’administrateur doit pouvoir :

- rechercher ;
- filtrer ;
- consulter ;
- modifier certaines données ;
- ajouter Calendly ;
- ajouter LinkedIn ;
- activer/désactiver ;
- récupérer le QR ;
- récupérer l’URL ;
- importer le CSV ;
- consulter le résultat d’import.

## 8. Cycle de vie

### Arrivée

Signitic → export CSV → import → carte créée.

### Modification

Les données changent mais l’URL et le QR restent identiques.

### Départ

La carte est désactivée. L’URL peut afficher un message neutre indiquant que la carte n’est plus disponible.

## 9. MVP

Le MVP doit couvrir :

- authentification admin ;
- collaborateurs ;
- import CSV ;
- création/mise à jour ;
- détection des absents ;
- carte publique ;
- QR stable ;
- vCard ;
- téléphone ;
- e-mail ;
- Calendly conditionnel ;
- LinkedIn conditionnel ;
- activation/désactivation ;
- récupération URL/QR ;
- journal d’audit basique.

Wallet et NFC sont des phases séparées.
