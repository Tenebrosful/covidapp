import 'ol/ol.css';
import Feature from 'ol/Feature';
import Geolocation from 'ol/Geolocation';
import Map from 'ol/Map';
import Point from 'ol/geom/Point';
import View from 'ol/View';
import {Circle as CircleStyle, Fill, Stroke, Style} from 'ol/style';
import {OSM, Vector as VectorSource} from 'ol/source';
import {Tile as TileLayer, Vector as VectorLayer} from 'ol/layer';

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
        fetch("/apiMap").then((response) => {
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
        resolve.forEach(positif => positionPositifs.push([positif.coordonate]));
        positionPositifs.forEach(coordinates => {
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
