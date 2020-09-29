<?php

namespace App\Domain\Localisation;

// Localisation est un modèle d'Eloquent
use Illuminate\Database\Eloquent\Model;
// On importe les entités dont on a besoin dans le code
use App\Domain\Utilisateur\Utilisateur;

class Localisation extends Model {
    /*
     * Nom de la table
     */
    protected $table = 'localisations';
    /*
     * Nom de la primary key
     */
    protected $primaryKey = 'id';
    /*
     * Liste des colones modifiables
     *
     * @var array
     */
    protected $fillable = [
        'latitude',
        'longitude'
    ];
    /*
     * Liste des colones à cacher en cas de conversion en String / JSON
     *
     * @var array
     */
    protected $hidden = [];
    /*
     * Une méthode pour retrouver l'utilisateur d'une localisation
     */
    public function utilisateur() {
        // One to one correct
        // Localisation belongs to Utilisateur (si 2 personnes peuvent être au même endroit)
        return $this->belongsTo('App\Domain\Utilisateur\Utilisateur');
        //return $this->belongsToMany('App\Domain\Utilisateur\Utilisateur', 'utilisateurlocalisation', 'id_user', 'id_localisation');
        //return $this->belongsToMany('App\Domain\Utilisateur\Utilisateur')->using('App\Domain\UtilisateurLocalisation');
        //return $this->hasOneThrough('App\Domain\Utilisateur\Utilisateur', 'App\Domain\UtilisateurLocalisation\UtilisateurLocalisation', 'id_localisation', 'id_user');
    }

}