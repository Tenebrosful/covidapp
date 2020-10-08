# Pour construire l'image
`docker build -t covidapp -f docker/Dockerfile .`
# Pour démarrer le container
`docker run -p 5050:80 -v "$PWD":/app covidapp`
