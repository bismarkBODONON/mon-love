<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

// HasApiTokens permet à Sanctum de générer/valider des tokens pour ce
// modèle, exactement comme il le ferait pour "User" dans une app classique.
// Ici, comme il n'y a qu'un seul "utilisateur" (le couple), on branche
// directement Sanctum sur RelationshipSetting plutôt que de créer une
// table "users" qui n'aurait aucun sens (pas d'inscription, pas de liste
// d'utilisateurs, un seul PIN partagé).
class RelationshipSetting extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'partner_name',
        'relationship_start_date',
        'pin_hash',
        'accent_color',
    ];

    protected $hidden = [
        'pin_hash', // On ne renvoie JAMAIS le hash du PIN au frontend
    ];

    protected $casts = [
        'relationship_start_date' => 'date',
    ];

    /**
     * Récupère (ou crée) l'unique ligne de configuration.
     * Pattern "singleton actif" : évite de gérer un ID en dur partout
     * dans le code, et évite les erreurs si la table est vide.
     */
    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'partner_name' => 'Mon amour',
                'relationship_start_date' => now(),
            ]
        );
    }
}
