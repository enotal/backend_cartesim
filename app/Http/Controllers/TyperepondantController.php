<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Typerepondant;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TyperepondantResource;

class TyperepondantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resource = Typerepondant::with('repondants')->with('sessiondemandes')->with('sessionremises')->orderBy('id', 'ASC')->get();
        return TyperepondantResource::collection($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => "required|string|unique:typerepondants,tyrcode",
            'libelle' => "required|string|unique:typerepondants,tyrlibelle",
            'active' => "string|size:3",
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : identifiant ou email existant !", 'status' => 201]);
        }

        $resource = Typerepondant::create([
            'tyrcode' => $request->code,
            'tyrlibelle' => $request->libelle,
            'tyractive' => $request->active,
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
        $resource = Typerepondant::with('repondants')->with('sessiondemandes')->with('sessionremises')->where("id", $id)->first(); 
        if ($resource){
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
            'code' => "required|string|unique:typerepondants,tyrcode," . $id,
            'libelle' => "required|string|unique:typerepondants,tyrlibelle," . $id,
            'active' => "string|size:3",
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : identifiant ou email existant !", 'status' => 201]);
        }

        $resource = Typerepondant::findOrFail($id)->update([
            'tyrcode' => $request->code,
            'tyrlibelle' => $request->libelle,
            'tyractive' => $request->active,
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
        $resource = ($id === "all") ? Typerepondant::truncate() : Typerepondant::findOrFail($id)->delete();

        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement supprimé !", 'status' => 200]);
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : suppression non effectuée !", 'status' => 201]);
    }
}
