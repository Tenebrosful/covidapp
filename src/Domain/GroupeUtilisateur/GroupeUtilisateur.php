<?php


namespace App\Domain\GroupeUtilisateur;


use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupeUtilisateur extends Pivot
{
    /*
     * Nom de la table
     */
    protected $table = 'groupeUtilisateur';

    /*
     * Nom de la primary key
     */
    protected $primaryKey = [
        'id_user',
        'id_groupe'
    ];

    protected $incrementing = false;

    /*
     * Liste des colones modifiables
     *
     * @var array
     */
    protected $fillable = [
        'id_groupe',
        'id_user'
    ];

    /*
     * Liste des colones à caché en cas de conversion en String / JSON
     *
     * @var array
     */
    protected $hidden = [];
}