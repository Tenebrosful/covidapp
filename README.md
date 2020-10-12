# CovidApp
*Développé par Hugo Bernard et David Baraniak*


## Initialiser le projet
*Require [composer](https://getcomposer.org "Site Officiel de composer")*

À la racine du projet :\
```bash
composer install
```

## Puis créer le répertoire mysqldata et build+run tous les containers
À la racine du projet :
```bash
mkdir mysqldata\
chmod 777 mysqldata\
docker-compose exec web php app/setupBDD.php\
docker-compose up
```