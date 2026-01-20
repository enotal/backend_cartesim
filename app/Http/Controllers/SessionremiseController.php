<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sessionremise;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\SessionremiseResource;

class SessionremiseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resource = Sessionremise::with('anneeacademique')->with('typerepondant')->with('demandes')->orderBy('id', 'ASC')->get();
        return SessionremiseResource::collection($resource);
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

        $resource = Sessionremise::create([
            'serdatedebut' => $request->datedebut,
            'serdatefin' => $request->datefin,
            'seractive' => $request->active,
            'sercommentaire' => $request->commentaire,
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
        $resource = Sessionremise::with('anneeacademique')->with('typerepondant')->with('demandes')->where('id', $id)->first();
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

        $resource = Sessionremise::findOrFail($id)->update([
            'serdatedebut' => $request->datedebut,
            'serdatefin' => $request->datefin,
            'seractive' => $request->active,
            'sercommentaire' => $request->commentaire,
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
        $resource = ($id === "all") ? Sessionremise::truncate() : Sessionremise::findOrFail($id)->delete();

        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement supprimé !", 'status' => 200]);
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : suppression non effectuée !", 'status' => 201]);
    }
}
