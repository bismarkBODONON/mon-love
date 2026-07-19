<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RelationshipSetting;
use Illuminate\Http\Request;

class RelationshipController extends Controller
{
    /**
     * On renvoie uniquement la date de début et les infos d'affichage.
     * Le CALCUL du compteur (années/mois/jours/heures/minutes/secondes)
     * se fait côté FRONTEND, pas ici.
     *
     * Pourquoi ? Parce que ce compteur doit "vivre" en temps réel,
     * seconde par seconde, sans jamais retaper le serveur. Faire un
     * appel API chaque seconde serait absurde en performance et en
     * consommation de données. Le backend fournit la source de vérité
     * (la date de départ), le frontend fait tourner l'horloge.
     */
    public function show()
    {
        $settings = RelationshipSetting::current();

        return response()->json([
            'partner_name' => $settings->partner_name,
            'relationship_start_date' => $settings->relationship_start_date->toIso8601String(),
            'accent_color' => $settings->accent_color,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'partner_name' => 'sometimes|string|max:100',
            'relationship_start_date' => 'sometimes|date',
            'accent_color' => 'sometimes|string|max:20',
        ]);

        $settings = RelationshipSetting::current();
        $settings->update($validated);

        return response()->json($settings);
    }
}
