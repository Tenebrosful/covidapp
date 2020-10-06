<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint as Blueprint;

require __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Suppression de clés primaires pour pouvoir supprimer les tables
if (Capsule::schema()->hasTable('messagerie')) {
    Capsule::schema()->table('messagerie', function (Blueprint $table) {
        $table->dropForeign(['id_user_auteur']);
        $table->dropForeign(['id_groupe']);
        $table->dropForeign(['id_message']);
    });
}
if (Capsule::schema()->hasTable('utilisateurLocalisation')) {
    Capsule::schema()->table('utilisateurLocalisation', function (Blueprint $table) {
        $table->dropForeign(['id_user']);
        $table->dropForeign(['id_localisation']);
    });
}
if (Capsule::schema()->hasTable('groupeUtilisateur')) {
    Capsule::schema()->table('groupeUtilisateur', function (Blueprint $table) {
        $table->dropForeign(['id_user']);
        $table->dropForeign(['id_groupe']);
    });
}

Capsule::schema()->dropIfExists('utilisateurs');
Capsule::schema()->create('utilisateurs', function ($table) {

    $table->increments('id');

    $table->string('email')->unique();

    $table->string('mdpCrypte');

    $table->string('nom');

    $table->string('prenom');

    $table->date('dateNais');

    $table->timestamps();

});

Capsule::schema()->dropIfExists('messages');
Capsule::schema()->create('messages', function ($table) {

    $table->increments('id');

    $table->string('contenu');

    $table->timestamp('date', 0);

    $table->timestamps();

});

Capsule::schema()->dropIfExists('groupe');
Capsule::schema()->create('groupe', function ($table) {

    $table->increments('id');

    $table->timestamps();

});

Capsule::schema()->dropIfExists('groupeUtilisateur');
Capsule::schema()->create('groupeUtilisateur', function ($table) {

    $table->integer('id_groupe')->unsigned();

    $table->integer('id_user')->unsigned();

    $table->foreign('id_user')->references('id')->on('utilisateurs')->onDelete('cascade');

    $table->foreign('id_groupe')->references('id')->on('groupe')->onDelete('cascade');

    $table->primary(['id_user', 'id_groupe']);

    $table->timestamps();
});

Capsule::schema()->dropIfExists('messagerie');
Capsule::schema()->create('messagerie', function ($table) {

    $table->integer('id_user_auteur')->unsigned();

    $table->integer('id_groupe')->unsigned();

    $table->integer('id_message')->unsigned()->unique();

    $table->foreign('id_user_auteur')->references('id')->on('utilisateurs')->onDelete('cascade');

    $table->foreign('id_groupe')->references('id')->on('groupe')->onDelete('cascade');

    $table->foreign('id_message')->references('id')->on('messages')->onDelete('cascade');

    $table->primary(['id_user_auteur', 'id_groupe', 'id_message'], 'messagerie_primarykeys');

    $table->timestamps();
});

Capsule::schema()->dropIfExists('localisations');
Capsule::schema()->create('localisations', function ($table) {

    $table->increments('id');

    $table->string('latitude');

    $table->string('longitude');
    $table->timestamps();

});

Capsule::schema()->dropIfExists('utilisateurlocalisation');
Capsule::schema()->create('utilisateurlocalisation', function ($table) {

    $table->integer('id_user')->unsigned();

    $table->integer('id_localisation')->unsigned();

    $table->foreign('id_user')->references('id')->on('utilisateurs')->onDelete('cascade');

    $table->foreign('id_localisation')->references('id')->on('localisations')->onDelete('cascade');

    $table->primary(['id_user', 'id_localisation']);
    $table->timestamps();

});
