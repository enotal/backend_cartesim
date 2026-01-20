<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sessiondemande;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\SessiondemandeResource;

class SessiondemandeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resource = Sessiondemande::with('anneeacademique')->with('typerepondant')->with('demandes')->orderBy('id', 'ASC')->get();
        return SessiondemandeResource::collection($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'datedebut' => "required|date",
            'datefin' => "required|date",
            'active' => "string|size:3",
            'commentaire' => "string|nullable",
            'anneeacademique' => "required|numeric",
            'typerepondant' => "required|numeric"
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : champ(s) mal renseigné(s) !", 'status' => 201]);
        }

        $resource = Sessiondemande::create([
            'seddatedebut' => $request->datedebut,
            'seddatefin' => $request->datefin,
            'sedactive' => $request->active,
            'sedcommentaire' => $request->commentaire,
            'anneeacademique_id' => $request->anneeacademique,
            'typerepondant_id' => $request->typerepondant
        ]);
        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement effectué !", 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : enregistrement non effectué !", 'status' => 201]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $resource = Sessiondemande::with('anneeacademique')->with('typerepondant')->with('demandes')->where('id', $id)->first();
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
            'datedebut' => "required|date",
            'datefin' => "required|date",
            'active' => "string|size:3",
            'commentaire' => "string|nullable",
            'anneeacademique' => "required|numeric",
            'typerepondant' => "required|numeric"
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : champ(s) mal renseigné(s) !", 'status' => 201]);
        }

        $resource = Sessiondemande::findOrFail($id)->update([
            'seddatedebut' => $request->datedebut,
            'seddatefin' => $request->datefin,
            'sedactive' => $request->active,
            'sedcommentaire' => $request->commentaire,
            'anneeacademique_id' => $request->anneeacademique,
            'typerepondant_id' => $request->typerepondant
        ]);
        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement édité !", 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : édition non effectuée !", 'status' => 201]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $resource = ($id === "all") ? Sessiondemande::truncate() : Sessiondemande::findOrFail($id)->delete();

        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement supprimé !", 'status' => 200]);
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : suppression non effectuée !", 'status' => 201]);
    }

    /**
     * Display the specified resource.
     */
    public function showBy(Request $request)
    {
        $keys  = explode(";", $request->search);
        $count = count($keys);

        if ($count === 0) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : aucune valeur fournie !", 'status' => 201]);
        }

        if ($count < 2) {
            $resource = Sessiondemande::with('typerepondant')->where('sed' . $keys[0], $request[$keys[0]])->first();
            if ($resource) {
                return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : 1 enregistrement trouvé !", "data" => $resource, 'status' => 200]);
            }
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : aucun enregistrement correspondant !", 'status' => 201]);
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : plus de 2 filtres fournis !", 'status' => 201]);
    }

    /**
     * Display the specified resource.
     */
    public function getActive()
    {
        $today = Date('Y-m-d');
        $resource = Sessiondemande::with('typerepondant')->where('sedactive', "oui")->where('seddatedebut', '<=', $today)->where('seddatefin', '>=', $today)->first();
        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : 1 enregistrement trouvé !", "data" => $resource, 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : aucun enregistrement correspondant !", "data" => [], 'status' => 201]);
    }
}
