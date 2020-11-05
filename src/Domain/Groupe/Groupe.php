<?php


namespace App\Domain\Groupe;


use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $colonne, string $comparateur, mixed $valeur)
 * @method static find(array $primaryKeys)
 */
class Groupe extends Model
{
    /**
     * Nom de la table
     */
    protected $table = 'groupe';

    /**
     * Nom de la primary key
     */
    protected $primaryKey = 'id';

    /**
     * Liste des colones modifiables
     *
     * @var array
     */
    protected $fillable = ['nom'];

    /**
     * Liste des colones à cacher en cas de conversion en String / JSON
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * @param int id
     * @throw ModelNotFoundException
     * @return Groupe
     */
    static public function getById($id)
    {
        return Groupe::find([$id])->first();
    }

    /**
     * Retourne les membres d'un groupe
     */
    public function membres()
    {
        return $this->belongsToMany("App\Domain\Utilisateur\Utilisateur", "groupeUtilisateur", "id_groupe", "id_user")->get();
    }

    /**
     * Retourne les messages postées dans un groupe
     */
    public function messages()
    {
        return $this->belongsToMany("App\Domain\Message\Message", "messagerie", "id_groupe", "id_message")->get();
    }
}