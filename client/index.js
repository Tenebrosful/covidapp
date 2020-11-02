// Importation Bootstrap
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';

/* Imports pour groupes.html */
import $ from 'jquery';

/* Imports pour map.html */
import 'ol/ol.css';
import Feature from 'ol/Feature';
import Geolocation from 'ol/Geolocation';
import Map from 'ol/Map';
import Point from 'ol/geom/Point';
import View from 'ol/View';
import {Circle as CircleStyle, Fill, Stroke, Style} from 'ol/style';
import {OSM, Vector as VectorSource} from 'ol/source';
import {Tile as TileLayer, Vector as VectorLayer} from 'ol/layer';

/* Pour groupes.html */

// Rajout de comportement à la liste de choix pour rajout de groupes
$('.rajoutmodificationmodals').each(function() {
    $(this).find('.list-group-item-action').on('click', function() {
        if ($(this).find('#groupChecked').text() === '✔')
            $(this).find('#groupChecked').text('');
        else
            $(this).find('#groupChecked').text('✔');
    });
});
// Lit quelles groupes on été choisies au submit du formulaire
$('.rajoutmodificationmodals').each(function() {
    $(this).find('form').on('submit', function(e) {
        let checkedUsers = [];
        $(this).parent().parent().find('.list-group-item-action').each(function() {
            if ($(this).find('#groupChecked').text() === '✔')
                checkedUsers.push($(this).find('#groupId').text());
        });
        checkedUsers.push($(this).find("[name='users']").val());
        $(this).find("[name='users']").val(checkedUsers.join());
    });
});
// On prépare le modal pour modifier les groupes en fonction du groupe choisie
$('.buttonmodificationgroupes').each(function() {
    $(this).on('click', function(event) {
        // Définition de la fonction pour récupérer les données en JSON
        function requeteRecuperation(groupid) {
            return new Promise((resolve, reject) => {
                fetch("/group/"+groupid).then((response) => {
                    if (response.ok)
                        return response.json();
                    else
                        reject(response.statusText);
                }).then((responsejson) => {
                    resolve(responsejson);
                }).catch((error) => {
                    reject(error);
                });
            });
        }
        // Appel à cette fonction
        requeteRecuperation($(this).data('groupid')).then((resolve) => {
            // On met le titre
            $('#modificationGroupesModalLabel').html("Modification du groupe <i>" + resolve[0].nom + "</i>");
            // On décoche tout
            $('#modificationGroupesModal').find('#groupChecked').each(function() {
                $(this).text('');
            });
            // Puis on coche uniquement les utilisateurs qui appartiennent déjà au groupe
            resolve[1].forEach((utilisateur) => {
                // Recherche dans la liste et si trouvé on coche
                $('#modificationGroupesModal').find('.list-group-item-action').each(function() {
                    if ($(this).find('#groupId').text() == utilisateur.id)
                        $(this).find('#groupChecked').text('✔');
                });
            });
            // On remplit le l'id et le nom dans le form
            $('#modificationGroupesModal').find('#inputGroupId').val(resolve[0].id);
            $('#modificationGroupesModal').find('#inputGroupTitle').val(resolve[0].nom);
            $('#modificationGroupesModal').modal('show');
        }).catch((reject) => {
            console.error(reject);
        });
    });
});

/* Pour map.html */

const view = new View({
    center: [0, 0],
    zoom: 15,
});

const map = new Map({
    layers: [
        new TileLayer({
            source: new OSM(),
        }) ],
    target: 'map',
    view: view,
});

const geolocation = new Geolocation({
    // enableHighAccuracy must be set to true to have the heading value.
    trackingOptions: {
        enableHighAccuracy: true,
    },
    projection: view.getProjection(),
});

function el(id) {
    return document.getElementById(id);
}

geolocation.setTracking(true);

const accuracyClient = new Feature();
geolocation.on('change:accuracyGeometry', function () {
    accuracyClient.setGeometry(geolocation.getAccuracyGeometry());
});

const positionClient = new Feature();
positionClient.setStyle(
    new Style({
        image: new CircleStyle({
            radius: 6,
            fill: new Fill({
                color: '#3399CC',
            }),
            stroke: new Stroke({
                color: '#fff',
                width: 2,
            }),
        }),
    })
);

let positionPositifs = [];
let pointsPosifits = [];

geolocation.on('change:position', function () {
    let coordinates = geolocation.getPosition();
    view.setCenter(coordinates);
    positionClient.setGeometry(coordinates ? new Point(coordinates) : null);
    const promise =  new Promise((resolve, reject) => {
        fetch("/apimap").then((response) => {
            if (response.ok)
                return response.json();
            else
                reject(response.statusText);
        }).then((responsejson) => {
            resolve(responsejson);
        }).catch((error) => {
            reject(error);
        });
    });

    promise.then((resolve) => {
        positionPositifs = [];
        pointsPosifits = []
        resolve.forEach(positif => positionPositifs.push([positif]));
        positionPositifs.forEach(coordinates => {
            console.log(coordinates);
            const point = new Feature();
            point.setStyle(
                new Style({
                    image: new CircleStyle({
                        radius: 6,
                        fill: new Fill({
                            color: '#000',
                        }),
                        stroke: new Stroke({
                            color: '#fff',
                            width: 2,
                        }),
                    }),
                })
            );
            point.setGeometry(coordinates ? new Point(coordinates) : null);
            pointsPosifits.push(point);
        })

    });
});

new VectorLayer({
    map: map,
    source: new VectorSource({
        features: [accuracyClient, positionClient, ...pointsPosifits],
    }),
});
