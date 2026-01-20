<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// use illuminate\Support\Facades\App;
// // Set the locale to French
// App::setLocale('fr');

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resource = User::with('demandes')->with('roles')->with('region')->with('province')->orderBy('id', 'ASC')->get();
        return UserResource::collection($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => "required|string",
            'prenoms' => "required|string",
            'email' => "required|string|unique:users,email",
            'sexe' => "required",
            'active' => "string|max:3",
            // 'statut' => "string|max:3",
            // 'password' => "string",
            'role' => "required",
            'province' => "nullable",
            'region' => "nullable",
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : email existant !", 'status' => 201]);
        }

        // Génération du mot de passe
        // $password = Hash::make('password'); 
        $password = password_hash('password', PASSWORD_BCRYPT);

        $resource = User::create([
            'name' => $request->nom,
            'lastname' => $request->prenoms,
            'email' => $request->email,
            'sexe' => $request->sexe,
            'active' => $request->active,
            // 'status' => $request->statut,
            'password' => $password,
            'province' => $request->province,
            'region' => $request->region,
        ]);
        if ($resource) {
            // Liaison avec les rôles => Table pivot = role_user
            $resource->roles()->detach();
            $resource->roles()->attach($request->role);
            // Liaison avec les régions => Table pivot = region_user
            // $resource->regions()->detach();
            // $resource->regions()->attach($request->region);
            // 
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement effectué !", 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : enregistrement non effectué !", 'status' => 201]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $resource = User::with('demandes')->with('roles')->with('region')->with('province')->where('id', $id)->first();
        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : 1 enregistrement trouvé !", 'data' => $resource, 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Succès : aucun enregistrement trouvé !", 'data' => $resource, 'status' => 201]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nom' => "required|string",
            'prenoms' => "required|string",
            'email' => "required|string|unique:users,email," . $id,
            'sexe' => "required",
            'active' => "string|max:3",
            // 'statut' => "string|max:3",
            // 'password' => "string", 
            'role' => "nullable",
            'province' => "nullable",
            'region' => "nullable",
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : email existant !" . $validator->errors(), 'status' => 201]);
        }

        // $password = Hash::make('password');
        $resource = User::findOrFail($id);
        $response = $resource->update([
            'name' => $request->nom,
            'lastname' => $request->prenoms,
            'email' => $request->email,
            'sexe' => $request->sexe,
            'active' => $request->active,
            'province_id' => $request->province,
            'region_id' => $request->region,
            // status => $request->statut
            // 'password' => $password,
        ]);

        if ($response) {
            // Liaison avec les rôles => Table pivot = role_user
            $resource->roles()->detach();
            $resource->roles()->attach($request->role);
            // Liaison avec les régions => Table pivot = region_user
            // $resource->regions()->detach();
            // $resource->regions()->attach($request->region);
            // 
            if ($response) {
                return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement édité !", 'status' => 200]);
            }
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : rôle(s) non attaché(s) !", 'status' => 201]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : édition non effectuée !", 'status' => 201]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function regeneratePassword(Request $request)
    {
        $resource = User::findOrFail($request->id);

        if ($resource) {
            $today = date('Y-m-d');
            $code = substr(sha1($today . time()), 0, 8);
            $newpassword = Hash::make($code);

            $response = $resource->update(['password' => $newpassword]);
            if ($response) {
                return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : mot de passe réinitialisé [" . $code . "]!", 'status' => 200]);
            }
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : mot de passe non réinitialisé !", 'status' => 201]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : enregistrement non trouvé !", 'status' => 201]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $resource = ($id === "all") ? User::truncate() : User::findOrFail($id)->delete();

        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement supprimé !", 'status' => 200]);
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : suppression non effectuée !", 'status' => 201]);
    }

    /**
     * Login.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => "required|string",
            'password' => "required|string",
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : email ou mot de passe requis !", 'status' => 201]);
        }

        $resource = User::with('roles')->where('email', $request->email)->first();

        if ($resource) {
            if ($resource->active === "oui") {
                $response = password_verify($request->password, $resource->password);
                if ($response) {
                    // Préparation des données 
                    $data = [
                        'id' => $resource->id,
                        'email' => $resource->email,
                        'name' => $resource->name,
                        'lastname' => $resource->lastname,
                        'sexe' => $resource->sexe,
                        'roles' => $resource->roles->pluck('rlelibelle'),
                    ];
                    // Mise à jour du statut
                    User::findOrFail($resource->id)->update(['status' => "oui"]);
                    // Création de la session de l'utilisateur 
                    session('cartesim.auth.' . $resource->id, $data);
                    // 
                    return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : connexion validée !", 'data' => $data, 'status' => 200]);
                }
                return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : email ou mot de passe incorrect !", 'status' => 201]);
            }
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : désolé, votre compte est désactivé !", 'status' => 201]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : email ou mot de passe incorrect !", 'status' => 201]);
    }

    /**
     * Logout.
     */
    public function logout(string $id)
    {
        // Mise à jour du statut
        $resource = User::findOrFail($id)->update(['status' => "non"]);
        // Destruction de la session de l'utilisateur 
        if ($resource) {
            session('cartesim.auth.' . $id, "");
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : déconnexion validée !", 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : déconnexion non validée !", 'status' => 201]);
    }
}
