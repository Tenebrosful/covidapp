<?php


namespace App\Domain\UtilisateurLocalisation;


use Illuminate\Database\Eloquent\Relations\Pivot;

class UtilisateurLocalisation extends Pivot
{
    /**
     * Nom de la table
     */
    protected $table = 'utilisateurlocalisation';

    /**
     * Nom de la primary key
     */
    protected $primaryKey = [
        'id_user',
        'id_localisation'
    ];

    protected $incrementing = false;

    /**
     * Liste des colones modifiables
     *
     * @var array
     */
    protected $fillable = [
        'id_user',
        'id_localisation'
    ];

    /**
     * Liste des colones à caché en cas de conversion en String / JSON
     *
     * @var array
     */
    protected $hidden = [];
}