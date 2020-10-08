# Pour construire l'image
`docker build -t covidapp -f docker/Dockerfile .`
# Pour démarrer le container
`docker run -p 5050:80 -v "$PWD":/app covidapp`

## À ignorer (pour explications uniquement)
`docker run --name mon-propre-mysql -e MYSQL_ROOT_PASSWORD=meilleurmdpaumonde -v "$PWD"/mysql:/var/lib/mysql -d mysql`\
`docker run --name mon-propre-phpmyadmin --link mon-propre-mysql:db -p 8081:80 -d phpmyadmin/phpmyadmin`
