<?php


namespace App\Domain\Utilisateur;

class Utilisateur extends \Illuminate\Database\Eloquent\Model
{
    /*
     * Nom de la table
     */
    protected $table = 'utilisateurs';

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
        'email',
        'mdpCrypte',
        'nom',
        'prenom',
        'dateNais'
    ];

    /*
     * Liste des colones à caché en cas de conversion en String / JSON
     *
     * @var array
     */
    protected $hidden = ['mdpCrypte'];
}