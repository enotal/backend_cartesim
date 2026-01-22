<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Repondant;
use Illuminate\Http\Request;
use App\Http\Resources\RepondantResource;
use Illuminate\Support\Facades\Validator;

class RepondantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resource = Repondant::with('typerepondant')->with('demandes')->orderBy('id', 'ASC')->get();
        return RepondantResource::collection($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifiant' => "required|string|unique:repondants,repidentifiant",
            'sexe' => "required|string",
            'email' => "required|string|unique:repondants,repemail",
            'active' => "string|size:3",
            'typerepondant' => "required|numeric"
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : identifiant ou email existant !", 'status' => 201]);
        }

        $resource = Repondant::create([
            'repidentifiant' => $request->identifiant,
            'repsexe' => $request->sexe,
            'repemail' => $request->email,
            'repactive' => $request->active,
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
        $resource = Repondant::with('typerepondant')->with('demandes')->where("id", $id)->first();
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
            'identifiant' => "required|string|unique:repondants,repidentifiant," . $id,
            'sexe' => "required|string",
            'email' => "required|string|unique:repondants,repemail," . $id,
            'active' => "string|size:3",
            'typerepondant' => "required|numeric"
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : identifiant ou email existant !", 'status' => 201]);
        }

        $resource = Repondant::findOrFail($id)->update([
            'repidentifiant' => $request->identifiant,
            'repsexe' => $request->sexe,
            'repemail' => $request->email,
            'repactive' => $request->active,
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
        $resource = ($id === "all") ? Repondant::truncate() : Repondant::findOrFail($id)->delete();

        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement supprimé !", 'status' => 200]);
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : suppression non effectuée !", 'status' => 201]);
    }


    /**
     * Import list of repondant.
     */
    public function import(Request $request)
    {
        $columns = [];
        foreach ($request->columns as $column) {
            if ($column['name'] === "ine" || $column['name'] === "matricule") {
                $columns['identifiant'] = $column['value'];
            } else {
                $columns[$column['name']] = $column['value'];
            }
        }

        $total = count($request->imports);
        $count = 0;
        $doublons = [];
        $undones = [];
        $i = 0;
        $j = 0;
        foreach ($request->imports as $import) {
            $resource = Repondant::where('repidentifiant', $import[$columns['identifiant']])->orWhere('repemail', $import[$columns['email']])->first();
            if ($resource) {
                $doublons[$i++] = $resource->repidentifiant;
                $undones[$j++] = $resource->repidentifiant;
            } else {
                $response = Repondant::create([
                    'repidentifiant' => $import[$columns['identifiant']],
                    'repsexe' => substr($import[$columns['sexe']], 0, 1) === "F" ? "Féminin" : "Masculin",
                    'repemail' => $import[$columns['email']],
                    'repactive' => "oui",
                    'typerepondant_id' => $request->typerepondant
                ]);
                if ($response) {
                    $count++;
                } else {
                    $undones[$j++] = $import[$columns['identifiant']];
                }
            }
        }

        // Tout a été importé !!! 
        if ($j < 1) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : $count importation(s) effectuée(s) / " .  $total . " !", 'data' => [], 'status' => 200]);
        } else {
            // Partiellement, à cause de doublons 
            if ($i > 0) {
                return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : " . $count . " importation(s) effectuée(s) / " . $total . " ! " . count($doublons) . " doublon(s) existants(s) !", 'data' => $doublons, 'status' => 201]);
            }
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : " . $count . " importation(s) effectuée(s) / " . $total . " ! " . count($undones) . " non importé(s) !", 'data' => $undones, 'status' => 202]);
        }
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
            $resource = Repondant::with('typerepondant')->with('demandes')->with('demandes.sessiondemande')->with('demandes.sim')->where('rep' . $keys[0], $request[$keys[0]])->first();
            if ($resource) {
                return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : 1 enregistrement trouvé !", "data" => $resource, 'status' => 200]);
            }
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : aucun enregistrement correspondant !", 'status' => 201]);
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : plus de 2 filtres fournis !", 'status' => 201]);
    }
}
