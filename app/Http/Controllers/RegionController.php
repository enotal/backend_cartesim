<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Resources\RegionResource;
use Illuminate\Support\Facades\Validator;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resource = Region::with('provinces')->with('provinces.sites')->with('sims')->orderBy('id', 'ASC')->get();
        return RegionResource::collection($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => "string|nullable|unique:regions,rgncode",
            'nom' => "required|string|unique:regions,rgnnom",
            'cheflieu' => "required|string|unique:regions,rgncheflieu",
            'active' => "string|size:3",
            'commentaire' => "string|nullable",
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : champ(s) mal renseigné(s) !", 'status' => 201]);
        }

        $resource = Region::create([
            'rgncode' => $request->code,
            'rgnnom' => $request->nom,
            'rgncheflieu' => $request->cheflieu,
            'rgnactive' => $request->active,
            'rgncommentaire' => $request->commentaire,
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
        $resource = Region::with('provinces')->with('provinces.sites')->with('sims')->where('id', $id)->first();
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
            'code' => "string|nullable|unique:regions,rgncode," . $id,
            'nom' => "required|string|unique:regions,rgnnom," . $id,
            'cheflieu' => "required|string|unique:regions,rgncheflieu," . $id,
            'active' => "string|size:3",
            'commentaire' => "string|nullable",
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : champ(s) mal renseigné(s) !", 'status' => 201]);
        }

        $resource = Region::findOrFail($id)->update([
            'rgncode' => $request->code,
            'rgnnom' => $request->nom,
            'rgncheflieu' => $request->cheflieu,
            'rgnactive' => $request->active,
            'rgncommentaire' => $request->commentaire,
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
        $resource = ($id === "all") ? Region::truncate() : Region::findOrFail($id)->delete();

        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement supprimé !", 'status' => 200]);
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : suppression non effectuée !", 'status' => 201]);
    }

    /**
     * Display the specified resource.
     */
    public function getActive()
    {
        $resource = Region::with('provinces')->with('provinces.sites')->where('rgnactive', "oui")->get();
        if ($resource->count() > 0) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : 1 enregistrement trouvé !", "data" => $resource, 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : aucun enregistrement correspondant !", "data" => $resource, 'status' => 201]);
    }
}
