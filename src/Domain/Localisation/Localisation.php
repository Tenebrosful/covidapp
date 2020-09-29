<?php

namespace App\Domain\Localisation;

// Localisation est un modèle d'Eloquent
use Illuminate\Database\Eloquent\Model;

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
        // One to one correct mais marche pas avec Pivot table
        return $this->belongsToMany('App\Domain\Utilisateur\Utilisateur', 'utilisateurlocalisation', 'id_localisation', 'id_user');
    }

}
