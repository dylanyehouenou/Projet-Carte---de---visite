# DÉPLOIEMENT

## Cible

Serveur contrôlé par la MMI’e.

## Pré-requis

- Docker ;
- Docker Compose ;
- domaine DNS ;
- HTTPS ;
- emplacement de sauvegarde séparé.

## Processus

1. récupérer le dépôt privé ;
2. créer `.env` ;
3. lancer les conteneurs ;
4. migrer la base ;
5. créer le premier admin ;
6. vérifier le health check ;
7. tester une carte ;
8. tester un import ;
9. sauvegarder.

## Production

Prévoir à terme :

GitHub Actions → tests/build → déploiement contrôlé.

La production ne doit pas dépendre d’un PC de développeur.

## Sauvegardes

Sauvegarder régulièrement :

- MariaDB ;
- photos ;
- configuration nécessaire à la restauration.

Tester régulièrement une restauration.
