<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebauthnCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebauthnController extends Controller
{
    /**
     * Le frontend demande les options de création.
     * On force 'localhost' en développement pour éviter l'erreur
     * "relying party ID is not a registrable domain suffix".
     */
    public function registerOptions(Request $request)
    {
        // Forcer 'localhost' en développement pour que WebAuthn fonctionne avec Vue sur localhost:5173
        $rpId = app()->environment('local') ? 'localhost' : $request->getHost();

        $challenge = Str::random(32);
        session(['webauthn_challenge' => $challenge]);

        return response()->json([
            'challenge' => base64_encode($challenge),
            'rp'        => [
                'name' => 'Notre Histoire',
                'id'   => $rpId,
            ],
            'user'      => [
                'id'          => base64_encode('couple-user-1'),
                'name'        => 'notre-histoire',
                'displayName' => 'Notre Histoire',
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7],   // ES256
                ['type' => 'public-key', 'alg' => -257], // RS256
            ],
            'authenticatorSelection' => [
                'authenticatorAttachment' => 'platform', // Touch ID / Face ID / Windows Hello
                'userVerification'        => 'required',
            ],
            'timeout' => 60000,
        ]);
    }

    /**
     * Le frontend renvoie la réponse de navigator.credentials.create() ici.
     * TODO: brancher le validateur du package web-auth/webauthn-lib
     * pour vérifier la signature cryptographique.
     */
    public function registerVerify(Request $request)
    {
        // TODO : remplacer par la vraie validation
        $credential = WebauthnCredential::create([
            'credential_id'   => $request->input('id'),
            'public_key_data' => json_encode($request->all()),
            'label'           => $request->input('label', 'Appareil'),
        ]);

        return response()->json($credential, 201);
    }

    /**
     * Options pour se connecter avec un facteur biométrique déjà enregistré.
     */
    public function loginOptions()
    {
        $challenge = Str::random(32);
        session(['webauthn_challenge' => $challenge]);

        $credentials = WebauthnCredential::all()->map(fn ($c) => [
            'type' => 'public-key',
            'id'   => $c->credential_id,
        ]);

        return response()->json([
            'challenge'        => base64_encode($challenge),
            'allowCredentials' => $credentials,
            'userVerification' => 'required',
            'timeout'          => 60000,
        ]);
    }

    /**
     * Vérification de la réponse de navigator.credentials.get().
     * TODO: brancher AuthenticatorAssertionResponseValidator.
     */
    public function loginVerify(Request $request)
    {
        // TODO : remplacer par la vraie validation
        return response()->json(['verified' => true]);
    }

    /**
     * Liste des credentials enregistrés.
     */
    public function index()
    {
        return WebauthnCredential::select('id', 'label', 'created_at')->get();
    }

    /**
     * Supprimer un credential.
     */
    public function destroy(WebauthnCredential $webauthnCredential)
    {
        $webauthnCredential->delete();
        return response()->json(null, 204);
    }
}