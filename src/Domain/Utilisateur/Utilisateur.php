<?php


namespace App\Domain\Utilisateur;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $colonne, string $comparateur, mixed $valeur)
 * @method static find(array $primaryKeys)
 */
class Utilisateur extends Model
{
    /**
     * Nom de la table
     */
    protected $table = 'utilisateurs';

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
        'email',
        'mdpCrypte',
        'nom',
        'prenom',
        'dateNais'
    ];

    /**
     * Liste des colones à caché en cas de conversion en String / JSON
     *
     * @var array
     */
    protected $hidden = ['mdpCrypte'];

    /**
     * Retourne la liste des messages envoyés par l'utilisateur
     */
    public function messagesEnvoyes()
    {
        return $this->belongsToMany("App\Domain\Message\Message", "messagerie", "id_user_auteur", "id_message")->get();
    }

    /**
     * @TODO Fix
     * Retourne la liste des messages reçus par l'utilisateur
     */
    public function messagesRecus()
    {
        return $this->belongsToMany("App\Domain\Message\Message", "messagerie", "id_groupe", "id_message")->get();
    }

    public function groupes()
    {
        return $this->belongsToMany("App\Domain\Groupe\Groupe", "groupeUtilisateur", "id_user", "id_groupe")->get();
    }

    public function localisations()
    {
        return $this->belongsToMany('App\Domain\Localisation\Localisation', 'utilisateurLocalisation', 'id_user', 'id_localisation')->get();
    }

    /**
     * @param string email
     * @throw ModelNotFoundException
     * @return Utilisateur
     */
    static public function getByEmail($email)
    {
        return Utilisateur::where('email', '=', $email)->firstOrFail();
    }

    /**
     * @param int id
     * @throw ModelNotFoundException
     * @return Utilisateur
     */
    static public function getById($id)
    {
        return Utilisateur::find([$id])->first();
    }
}
