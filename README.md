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

Attention, étant donné que l'on a un système d'authentification et vu que la table User est vide lors de l'initialisation de la base de données, il faut insérer au moins un utilisateur admin pour pouvoir s'authentifier (pour plus de détails, se reporter à la section[Système d'authentification](#système-dauthentification)) :

```sql
--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `name`) VALUES
(1, 'admin.admin@admin.com', '[\"ROLE_ADMIN\"]', exempleDeMotDePasseHashe, 'admin');
-- exempleDeMotDePasseHashe est à remplacer par votre propre mot de passe hashé

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

  --
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
```

* Pour simplifier la création d'une interface utilisateur, on utilise le framework CSS Bootstrap. Pour cela, on doit installer le composant AssetMapper :

```shell
composer require symfony/asset-mapper symfony/asset symfony/twig-pack
```

On doit aussi préciser quel fichier css importer depuis Bootstrap :

```shell
symfony console importmap:require bootstrap/dist/css/bootstrap.min.css
```

Il faut maintenant importer le **bootstrap.min.css**. Normalement c'est déjà fait, sinon ajouter la ligne suivante en haut de [assets/app.js](assets/app.js):

```js
import 'bootstrap/dist/css/bootstrap.min.css';
```

* Pour démarrer l'application, on démarre le serveur de développement intégré à Symfony :
```shell
symfony server:start -d
```
L'option -d permet de faire tourner le serveur en arrière-plan. Par défaut, sur votre machine, l'URL commence par **localhost:8000**.
Par exemple, pour aller sur la page de login, l'adresse web sera **localhost:8000/login**.

## Sécurité

La configuration sécuritaire est définie dans le fichier [security.yaml](config/packages/security.yaml).

### Système d'authentification

On implémente un système d'authentification pour différencier les utilisateurs
administrateurs et standards. Cette différentiation est définie par l'attribut roles, qui est un tableau indexé :
* ROLE_USER pour les utilisateurs standards
* ROLE_ADMIN pour les administrateurs


### Organisation des routes par préfixes
Pour des raisons de sécurité, on crée des routes distinctes pour les fonctionnalités administratives et les fonctionnalités
utilisateur. Les routes administratives auront un préfixe /admin/app et les routes utilisateurs /user/app.
De plus, lorsqu'un utilisateur demande une route existante, si celui-ci est déconnecté ou n'a pas d'autorisation d'accès, alors il est redirigé automatiquement vers la page de connexion. 

### Note
**Pour une première utilisation, ne pas oublier d'abord de créer un admin dans la base de données via la table user.**

## Fonctionnalités principales

### Architecture MVC

Nous adoptons une structure Model-View-Controller pour ce projet : 
* Le Model englobe la logique des données, c'est-à-dire, comment elles sont définies et organisées, les opérations que l'on peut faire dessus.
* Le Controller analyse la requête HTTP envoyée par le client et exécute le code associé pour la logique métier. Il permet d'adresser le Model, de récupérer les données du Model et de renvoyer ces dernières à la Vue dans la réponse HTTP.
* La View s'occupe de l'interface graphique.

### Ce que permet l'application

L'application permet :

* la gestion des livres :
    * Visualisation des livres sous forme de tableau. Chaque livre a un titre, un auteur, un genre et une date de publication.
    * Ajouter, modifier, et supprimer un livre
* la gestion des auteurs :
    * Visualisation des auteurs sous forme de liste. Chaque auteur a un nom, un prénom et une biographie.
    * Ajouter, modifier, et supprimer un auteur
* la gestion des emprunts :
    * Visualisation des emprunts sous forme de tableau. Chaque emprunt a un nom d'utilisateur, le titre du livre emprunté et la date d'emprunt
* la gestion des utilisateurs :
    * Visualisation des utilisateurs sous forme de tableau.

**IMPORTANT : Les actions de création, modification et suppression doivent être réservées aux
administrateurs. De plus, seul ces derniers ont accès aux utilisateurs (avec les opération CRUD associées).**

Exemple : si je veux afficher les livres, la méthode *bookList* du Controller *BookController* est appelée. La route associée *app_book_list* aura pour chemin */user/app/books/book-list* en n'oubliant pas de préfixer *localhost:8000*. Pour chaque livre, les admins auront droit à un bouton bleu de modification redirigeant vers un formulaire, un bouton rouge de suppression. Les admins auront aussi en haut de la page un bouton vert d'ajout de livre redirigeant vers un formulaire.

## À ajouter

BONUS : Rechercher un livre par titre ou auteur.
