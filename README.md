# Projet Application Web Météo
Par STRACK Ruben

## Installation et Initialisation

Pour installer les composants nécessaires au projet:

```
composer install
```

Pour connecter le projet à votre base de données:
- Copier le fichier `.env` en `.env.local`
- Mettez cette ligne dans ce fichier:
  - `DATABASE_URL="mysql://<login>:<password>@mysql/<database_name>?serverVersion=mariadb-10.2.25&charset=utf8"`
- Remplacez les éléments entre `<>` par vos valeurs
  - Remplacez également les `mysql` et le `serverVersion` selon votre serveur SQL
- Tapez la commande suivante pour créer les tables dans la base de donnée:
  - `composer db`

**Pour se connecter au compte administrateur:**
```angular2html
email: rubstr@example.com
password: test123
```

**Pour se connecter au compte d'un utilisateur normal:**
```angular2html
email: test@example.com
password: test123
```
