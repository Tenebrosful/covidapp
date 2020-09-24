<?php


namespace App\Domain\Localisation;


class Localisation extends \Illuminate\Database\Eloquent\Model
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