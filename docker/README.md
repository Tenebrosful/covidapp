# Pour construire l'image
`docker build -t covidapp -f docker/Dockerfile .`
# Pour démarrer le container
`docker run -p 5050:80 covidapp`
# Pour démarrer le container en mode développement (pas besoin de recréer l'image)
`docker run -p 5050:80 -v "$PWD":/app covidapp`
