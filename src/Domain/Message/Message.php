<?php


namespace App\Domain\Message;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $colonne, string $comparateur, mixed $valeur)
 * @method static find(array $primaryKeys)
 */
class Message extends Model
{
    /**
     * Nom de la table
     */
    protected $table = 'messages';

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
        'contenu',
        'date'
    ];

    /**
     * Liste des colones à caché en cas de conversion en String / JSON
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * @param int id
     * @throw ModelNotFoundException
     * @return Message
     */
    static public function getById($id)
    {
        return Message::find([$id])->first();
    }
}