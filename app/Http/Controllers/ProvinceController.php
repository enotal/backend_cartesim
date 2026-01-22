<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;
use App\Http\Resources\ProvinceResource;
use Illuminate\Support\Facades\Validator;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resource = Province::with('sites')->with('sims')->with('region')->orderBy('id', 'ASC')->get();
        return ProvinceResource::collection($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => "string|nullable|unique:provinces,prvcode",
            'nom' => "required|string|unique:provinces,prvnom",
            'cheflieu' => "required|string|unique:provinces,prvcheflieu",
            'active' => "string|size:3",
            'commentaire' => "string|nullable",
            'region' => "required|numeric",
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : champ(s) mal renseigné(s) !", 'status' => 201]);
        }

        $resource = Province::create([
            'prvcode' => $request->code,
            'prvnom' => $request->nom,
            'prvcheflieu' => $request->cheflieu,
            'prvactive' => $request->active,
            'prvcommentaire' => $request->commentaire,
            'region_id' => $request->region,
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
        $resource = Province::with('sites')->with('sims')->with('region')->where('id', $id)->first();
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
            'code' => "string|nullable|unique:provinces,prvcode," . $id,
            'nom' => "required|string|unique:provinces,prvnom," . $id,
            'cheflieu' => "required|string|unique:provinces,prvcheflieu," . $id,
            'active' => "string|size:3",
            'commentaire' => "string|nullable",
            'region' => "required|numeric",
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : champ(s) mal renseigné(s) !", 'status' => 201]);
        }

        $resource = Province::findOrFail($id)->update([
            'prvcode' => $request->code,
            'prvnom' => $request->nom,
            'prvcheflieu' => $request->cheflieu,
            'prvactive' => $request->active,
            'prvcommentaire' => $request->commentaire,
            'region_id' => $request->region,
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
        $resource = ($id === "all") ? Province::truncate() : Province::findOrFail($id)->delete();

        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement supprimé !", 'status' => 200]);
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : suppression non effectuée !", 'status' => 201]);
    }
}
