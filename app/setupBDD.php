<?php

use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';

$capsule = new Capsule;


if (is_array($bddConfig = parse_ini_file('../config/bdd.ini')))
    $capsule->addConnection($bddConfig);
else {
    echo "ERREUR : Fichier de configuration de la base de donnée introuvable";
    exit(); 
}

//Make this Capsule instance available globally.
$capsule->setAsGlobal();

// Setup the Eloquent ORM.
$capsule->bootEloquent();

Capsule::schema()->create('utilisateurs', function ($table) {

    $table->increments('id');

    $table->string('email')->unique();

    $table->string('mdpCrypté');

    $table->string('nom');

    $table->string('prenom');

    $table->date('dateNais');

    $table->timestamps();

});

Capsule::schema()->create('messages', function ($table) {

    $table->increments('id');

    $table->string('contenu');

    $table->date('date');

});

Capsule::schema()->create('messagerie', function ($table) {

    $table->foreign('id_user_auteur')->references('id')->on('utilisateurs')->onDelete('cascade');

    $table->foreign('id_user2_destinataire')->references('id')->on('utilisateurs')->onDelete('cascade');

    $table->foreign('id_message')->references('id')->on('messages')->onDelete('cascade');

});

Capsule::schema()->create('localisation', function ($table) {

    $table->increments('id');

    $table->string('latitude');

    $table->string('longitude');

});

Capsule::schema()->create('utlisateurlocalisation', function ($table) {

    $table->foreign('id_user')->references('id')->on('utilisateurs')->onDelete('cascade');

    $table->foreign('id_localisation')->references('id')->on('localisation')->onDelete('cascade');

});
