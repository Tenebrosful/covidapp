<?php


namespace App\Domain\Utilisateur;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $colonne, string $comparateur, mixed $valeur)
 */
class Utilisateur extends Model
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

    /*
     * Retourne la liste des messages envoyés par l'utilisateur
     */
    public function messagesEnvoyes()
    {
        return $this->belongsToMany("App\Domain\Message\Message", "messagerie", "id_user_auteur", "id_message")->get();
    }

    /*
     * Retourne la liste des messages reçus par l'utilisateur
     */
    public function messagesRecus()
    {
        return $this->belongsToMany("App\Domain\Message\Message", "messagerie", "id_user_destinataire", "id_message")->get();
    }

    public function localisations()
    {
        return $this->belongsToMany('App\Domain\Localisation\Localisation', 'utilisateurlocalisation', 'id_user', 'id_localisation');
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
}
