# MASTER — CAHIER DES CHARGES
## Cartes de visite virtuelles MMI’e

### 1. Contexte
La MMI’e souhaite mettre à disposition de ses collaborateurs une solution de carte de visite virtuelle professionnelle.

### 2. Objectif
Créer une solution simple à utiliser, simple à administrer et simple à maintenir, sans complexité technique inutile.

### 3. Fonctionnalités métier
- Carte numérique individuelle par collaborateur
- Partage depuis smartphone
- Enregistrement rapide dans les contacts via vCard
- QR code individuel
- URL permanente
- Apple Wallet et Google Wallet
- Étude du partage par proximité/NFC
- Respect de la charte graphique MMI’e
- Mise à jour centralisée
- Lien Calendly ou autre outil de rendez-vous si renseigné
- Import périodique du CSV Signitic
- Administration des cartes

### 4. Données
Champs minimum :
- prénom
- nom
- fonction
- service
- email
- téléphone
- adresse postale
- site internet MMI’e
- photo facultative
- LinkedIn facultatif
- Calendly facultatif
- identifiant de carte / QR

Une donnée absente ne doit jamais créer un bouton vide.

### 5. Carte publique
Format d'URL cible :
`carte.mmi-e.fr/prenom-nom`

La page affiche :
- Prénom NOM
- Fonction
- Service
- logo MMI’e
- site internet
- téléphone
- email
- photo si disponible

Actions conditionnelles :
- Ajouter à mes contacts
- Appeler
- Envoyer un e-mail
- Prendre rendez-vous

### 6. QR
Le QR est unique et stable.
Il encode uniquement l'URL permanente de la carte.
Une modification des coordonnées ne doit donc pas imposer de refaire le QR.

Le QR doit être :
- affichable
- intégré à la carte
- téléchargeable en image
- réutilisable sur signature mail, tour de cou, PPT, flyers, etc.

### 7. Import Signitic
L'administrateur importe un CSV.
Le système doit :
- reconnaître les collaborateurs déjà connus
- mettre à jour leurs informations
- créer les nouveaux
- signaler autant que possible les absents du nouveau fichier
- ne pas supprimer automatiquement un collaborateur absent

L'administrateur peut désactiver une carte.

### 8. Administration
Liste des collaborateurs avec recherche et filtres.
Actions :
- consulter la carte
- modifier les champs autorisés
- ajouter Calendly
- ajouter LinkedIn
- activer/désactiver
- récupérer le QR
- récupérer l'URL
- importer CSV

### 9. Arrivées / modifications / départs
Arrivée :
Signitic -> export CSV -> import -> création de carte.

Modification :
mise à jour des informations sans changer l'URL ni le QR.

Départ :
désactivation de la carte et affichage d'un message neutre.

### 10. Sécurité
- Interface admin protégée
- contrôle des rôles
- validation stricte du CSV
- protection contre les fichiers malveillants
- échappement des noms et contenus affichés
- noms de fichiers internes générés côté serveur
- stockage privé des fichiers
- journalisation des actions administratives
- sauvegardes documentées
- secrets hors Git

### 11. Critères d'acceptation MVP
Un administrateur doit pouvoir importer le CSV, obtenir les cartes, modifier un collaborateur, télécharger son QR, désactiver une carte et vérifier que l'URL reste stable après modification.

Un visiteur externe doit pouvoir ouvrir une carte publique depuis son QR, appeler, envoyer un mail et télécharger une vCard lorsque les données existent.
