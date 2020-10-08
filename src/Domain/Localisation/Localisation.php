<?php

namespace App\Domain\Localisation;

// Localisation est un modèle d'Eloquent
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $colonne, string $comparateur, mixed $valeur)
 * @method static find(array $primaryKeys)
 */
class Localisation extends Model {
    /**
     * Nom de la table
     */
    protected $table = 'localisations';
    /**
     * Nom de la primary key
     */
    protected $primaryKey = 'id';
    /**
     * Liste des colones modifiables
     *
     * @var array
     */
    protected $fillable = [
        'latitude',
        'longitude'
    ];
    /**
     * Liste des colones à cacher en cas de conversion en String / JSON
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Une méthode pour retrouver l'utilisateur d'une localisation
     */
    public function utilisateur()
    {
        return $this->belongsToMany('App\Domain\Utilisateur\Utilisateur', 'utilisateurLocalisation', 'id_localisation', 'id_user');
    }

    /**
     * @param int email
     * @throw ModelNotFoundException
     * @return Localisation
     */
    static public function getById($id)
    {
        return Localisation::find([$id])->first();
    }

}
