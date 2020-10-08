<?php


namespace App\Domain\Messagerie;


use Illuminate\Database\Eloquent\Relations\Pivot;

class Messagerie extends Pivot
{
    /**
     * Nom de la table
     */
    protected $table = 'messagerie';

    /**
     * Nom de la primary key
     */
    protected $primaryKey = [
        'id_user_auteur',
        'id_user_destinataire',
        'id_message'
    ];

    protected $incrementing = false;

    /**
     * Liste des colones modifiables
     *
     * @var array
     */
    protected $fillable = [
        'id_user_auteur',
        'id_user_destinataire',
        'id_message'
    ];

    /**
     * Liste des colones à caché en cas de conversion en String / JSON
     *
     * @var array
     */
    protected $hidden = [];
}