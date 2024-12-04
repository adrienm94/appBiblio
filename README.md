# appBiblio

Une petite application web permettant de gérer une bibliothèque. L’application doit permettre de gérer des livres, leurs auteurs et les emprunts par les utilisateurs. Elle est développée en [PHP](https://www.php.net/) à l'aide du framework [Symfony](https://symfony.com/).
Pour plus de détail, voir la section [Fonctionnalités principales](#Fonctionnalités principales)

## Installation

* Dans votre répertoire de travail, cloner le dépôt git :

```shell
git clone https://github.com/adrienm94/appBiblio
```

* Se placer à la racine du projet Symfony :

```shell
cd appBiblio
```

* À ce stade, il est nécessaire d'avoir sur sa machine :
    * PHP [(lien de téléchargement)](https://www.php.net/downloads.php)
    * Symfony (https://symfony.com/) [(lien de téléchargement)](https://symfony.com/download)
    * Le gestionnaire de dépendances Composer [(lien de téléchargement)](https://getcomposer.org/download)

* Ensuite, on se sert de la ligne de commande :
```shell
composer install
```
Cela permettra de lire le fichier [composer.json](composer.json), résoudre les dépendances et les installer dans le répertoire [vendor](vendor).

* En ce qui concerne la configuration de la connexion à la base de données (BD), on la définit la variable constante **DATABASE_URL** dans un fichier .env. Par exemple :

```shell
# DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
```

Ici *mysql* est le driver permettant de se connecter à la BD. Le *app* après les *://* est le nom de l'utilisateur connecté, le *!changeMe!* est son mot de passe. *127.0.0.1:3306* représente l'adresse du serveur de BD, tandis que le *app* juste après donne le nom de la BD. Dans les paramètres URL, on peut préciser la version du serveur et l'encodage des données.

Le système de gestion de bases de données relationnelles utilisé est [MySQL](https://www.mysql.com/).

* Pour simplifier la création d'une interface utilisateur, on utilise le framework CSS Bootstrap. Pour cela, on doit installer le composant AssetMapper :

```shell
composer require symfony/asset-mapper symfony/asset symfony/twig-pack
```

On doit aussi préciser quel fichier css importer depuis Bootstrap :

```shell
symfony console importmap:require bootstrap/dist/css/bootstrap.min.css
```

Il faut maintenant importer le **bootstrap.min.css**. Normalement c'est déjà fait, sinon ajouter la ligne suivante Dans [assets/app.js](assets/app.js):

```js
import 'bootstrap/dist/css/bootstrap.min.css';
```





## Fonctionnalités principales

> penser à expliquer authentification (security.yaml) + point d'entrée (symfony server:start sur localhost:8000 par défaut)