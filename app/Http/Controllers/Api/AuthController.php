<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RelationshipSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Pourquoi pas un système d'utilisateurs classique (register/login/email) ?
 * -> Cette application n'a qu'UNE seule personne qui l'utilise (ta copine).
 *    Un système d'auth email/mot de passe classique ajouterait de la
 *    complexité inutile (reset password, vérification email...) pour
 *    un problème qui n'existe pas ici. Un PIN à 4-6 chiffres + biométrie
 *    côté app mobile est largement suffisant et plus adapté à l'usage
 *    (déverrouillage rapide plusieurs fois par jour).
 *
 * Le token Sanctum sert uniquement à protéger l'API une fois le PIN
 * validé, pas à distinguer plusieurs comptes.
 */
class AuthController extends Controller
{
    /**
     * Nombre de tentatives de PIN autorisées avant blocage temporaire.
     */
    private const MAX_PIN_ATTEMPTS = 5;

    /**
     * Durée du blocage (en secondes) une fois la limite atteinte.
     */
    private const PIN_LOCKOUT_SECONDS = 30;

    public function hasPin(Request $request)
    {
        $settings = RelationshipSetting::current();

        return response()->json([
            'has_pin' => !is_null($settings->pin_hash),
        ]);
    }

    public function setPin(Request $request)
    {
        $validated = $request->validate([
            'pin' => 'required|string|min:4|max:8',
        ]);

        $settings = RelationshipSetting::current();
        $settings->update([
            'pin_hash' => Hash::make($validated['pin']),
        ]);

        return response()->json(['message' => 'PIN configuré avec succès.']);
    }

    public function verifyPin(Request $request)
    {
        $validated = $request->validate([
            'pin' => 'required|string',
        ]);

        // Clé de limitation basée sur l'IP : un seul utilisateur possible
        // sur cette app, donc pas de notion de "compte" avant ce point.
        $throttleKey = 'verify-pin:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_PIN_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'pin' => ["Trop de tentatives. Réessaie dans {$seconds} secondes."],
            ]);
        }

        $settings = RelationshipSetting::current();

        if (!$settings->pin_hash || !Hash::check($validated['pin'], $settings->pin_hash)) {
            // Échec : on enregistre la tentative avec la durée de blocage.
            RateLimiter::hit($throttleKey, self::PIN_LOCKOUT_SECONDS);

            throw ValidationException::withMessages([
                'pin' => ['PIN incorrect.'],
            ]);
        }

        // Succès : on réinitialise le compteur pour cette IP.
        RateLimiter::clear($throttleKey);

        // On utilise un "faux" modèle utilisateur unique pour Sanctum,
        // ou directement RelationshipSetting si tu ajoutes le trait
        // HasApiTokens dessus. Voir note dans routes/api.php.
        $token = $settings->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'partner_name' => $settings->partner_name,
        ]);
    }

    /**
     * Changer le PIN actuel (nécessite d'être authentifié)
     */
    public function changePin(Request $request)
    {
        $validated = $request->validate([
            'current_pin' => 'required|string|min:4|max:8',
            'new_pin'     => 'required|string|min:4|max:8|confirmed',
        ]);

        $settings = RelationshipSetting::current();

        // Vérifier le PIN actuel
        if (!$settings->pin_hash || !Hash::check($validated['current_pin'], $settings->pin_hash)) {
            throw ValidationException::withMessages([
                'current_pin' => ['Le PIN actuel est incorrect.'],
            ]);
        }

        // Mettre à jour avec le nouveau PIN
        $settings->update([
            'pin_hash' => Hash::make($validated['new_pin']),
        ]);

        return response()->json(['message' => 'PIN modifié avec succès.']);
    }
}