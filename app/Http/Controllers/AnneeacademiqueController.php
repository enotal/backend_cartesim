<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anneeacademique;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\AnneeacademiqueResource;

class AnneeacademiqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resource = Anneeacademique::with('sessiondemandes')->with('sessionremises')->with('sims')->orderBy('id', 'ASC')->get();
        return AnneeacademiqueResource::collection($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'code' => "required|string|unique:anneeacademiques,acacode",
            'datedebut' => "required|date|unique:anneeacademiques,acadatedebut",
            'datefin' => "required|date|unique:anneeacademiques,acadatefin",
            'active' => "string|size:3",
            'commentaire' => "string|nullable",
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : champ(s) mal renseigné(s) !", 'status' => 201]);
        }

        $code = Date('Y', strtotime($request->datedebut)) . "-" . Date('Y', strtotime($request->datefin));

        $resource = Anneeacademique::create([
            'acacode' => $code,
            'acadatedebut' => $request->datedebut,
            'acadatefin' => $request->datefin,
            'acaactive' => $request->active,
            'acacommentaire' => $request->commentaire
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
        $resource = Anneeacademique::with('sessiondemandes')->with('sessionremises')->with('sims')->where('id', $id)->first();
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
            // 'code' => "required|string|unique:anneeacademiques,acacode," . $id,
            'datedebut' => "required|date|unique:anneeacademiques,acadatedebut," . $id,
            'datefin' => "required|date|unique:anneeacademiques,acadatefin," . $id,
            'active' => "string|size:3",
            'commentaire' => "string|nullable",
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : champ(s) mal renseigné(s) !", 'status' => 201]);
        }

        $code = Date('Y', strtotime($request->datedebut)) . "-" . Date('Y', strtotime($request->datefin));

        $resource = Anneeacademique::findOrFail($id)->update([
            'acacode' => $code,
            'acadatedebut' => $request->datedebut,
            'acadatefin' => $request->datefin,
            'acaactive' => $request->active,
            'acacommentaire' => $request->commentaire
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
        $resource = ($id === "all") ? Anneeacademique::truncate() : Anneeacademique::findOrFail($id)->delete();

        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement supprimé !", 'status' => 200]);
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : suppression non effectuée !", 'status' => 201]);
    }

    /**
     * Display the specified resource.
     */
    public static function getCurrent(string $request)
    {
        $today = date('Y-m-d');
        $resource = Anneeacademique::with('sessiondemandes')->with('sessionremises')->with('sims')->with('sessiondemandes.demandes')->where('acadatedebut', '<=', $today)->where('acadatefin', '>=', $today)->first();
        if ($resource) {
            if ($request === 'resource') {
                return $resource;
            }
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : 1 enregistrement trouvé !", "data" => $resource, 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : aucun enregistrement correspondant !", "data" => $resource, 'status' => 201]);
    }
}
