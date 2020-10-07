# CovidApp
*Développé par Hugo Bernard et David Baraniak*


## Initialiser le projet
*Require [composer](https://getcomposer.org "Site Officiel de composer")*

À la racine du projet :\
`bash
composer install
`

## Ensuite créer l'image covidapp (voir répertoire docker)
`cd .docker`

## Puis créer le répertoire mysqldata et run tous les containers
`mkdir mysqldata`\
`chmod 777 mysqldata`\
`docker-compose up`
