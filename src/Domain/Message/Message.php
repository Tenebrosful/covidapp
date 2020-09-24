<?php


namespace App\Domain\Message;


class Message extends \Illuminate\Database\Eloquent\Model
{
    /*
     * Nom de la table
     */
    protected $table = 'messages';

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
        'contenu',
        'date'
    ];

    /*
     * Liste des colones à caché en cas de conversion en String / JSON
     *
     * @var array
     */
    protected $hidden = [];
}