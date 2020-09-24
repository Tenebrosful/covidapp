<?php


namespace App\Domain\Localisation;


use Illuminate\Database\Eloquent\Model;

class Localisation extends Model
{
    /*
     * Nom de la table
     */
    protected $table = 'localisation';

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
}