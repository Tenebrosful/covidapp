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

    $table->string('sel');

    $table->string('nom');

    $table->string('prenom');

    $table->date('dateNais');

    $table->timestamps();

});
