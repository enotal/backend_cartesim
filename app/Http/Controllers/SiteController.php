<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Resources\SiteResource;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resource = Site::with('demandes')->with('province')->with('province.region')->orderBy('id', 'ASC')->get();
        return SiteResource::collection($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'libelle' => "required|string|unique:sites,sitlibelle",
            'active' => "string|size:3",
            'commentaire' => "string|nullable",
            'province_id' => "numeric|nullable",
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : champ(s) mal renseigné(s) !", 'status' => 201]);
        }

        $resource = Site::create([
            'sitlibelle' => $request->libelle,
            'sitcommentaire' => $request->commentaire,
            'sitactive' => $request->active,
            'province_id' => $request->province ? $request->province : null,
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
        $resource = Site::with('demandes')->with('province')->with('province.region')->where('id', $id)->first();
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
            'libelle' => "required|string|unique:sites,sitlibelle," . $id,
            'commentaire' => "string|nullable",
            'active' => "string|size:3",
            'province_id' => "numeric|nullable",
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : identifiant ou email existant !", 'status' => 201]);
        }

        $resource = Site::findOrFail($id)->update([
            'sitlibelle' => $request->libelle,
            'sitcommentaire' => $request->commentaire,
            'sitactive' => $request->active,
            'province_id' => $request->province ? $request->province : null,
        ]);
        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement effectué !", 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : enregistrement non effectué !", 'status' => 201]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $resource = ($id === "all") ? Site::truncate() : Site::findOrFail($id)->delete();

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
        $resource = Site::where('sitactive', "oui")->get();
        if ($resource->count() > 0) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : 1 enregistrement trouvé !", "data" => $resource, 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : aucun enregistrement correspondant !", "data" => $resource, 'status' => 201]);
    }
}
