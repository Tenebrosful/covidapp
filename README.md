# covidapp
*Développé par Hugo Bernard et David Baraniak*


## Initialiser le projet
*Require [composer](https://getcomposer.org "Site Officiel de composer")*

À la racine du projet :\
```bash
composer install
npm install
npm start
```

Créer le fichier `config/bdd.ini` comme expliqué dans le README du même dossier

## Puis créer le répertoire mysqldata et build+run tous les containers
À la racine du projet :
```bash
mkdir mysqldata\
chmod 777 mysqldata\
docker-compose up\
docker-compose exec web php app/setupBDD.php
```