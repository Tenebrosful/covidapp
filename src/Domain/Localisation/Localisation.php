<?php


namespace App\Domain\Localisation;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Utilisateur\Utilisateur;

class Localisation extends Model
{
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
     * Liste des colones à caché en cas de conversion en String / JSON
     *
     * @var array
     */
    protected $hidden = [];

    public function utilisateur() {
        // Un seul utilisateur pour chaque localisation
        return $this->hasOne('App\Domain\Utilisateur');
    }

}