<?php


namespace App\Domain\Groupe;


use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    /*
     * Nom de la table
     */
    protected $table = 'groupe';

    /*
     * Nom de la primary key
     */
    protected $primaryKey = 'id';

    /*
     * Liste des colones modifiables
     *
     * @var array
     */
    protected $fillable = [];

    /*
     * Liste des colones à caché en cas de conversion en String / JSON
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
}