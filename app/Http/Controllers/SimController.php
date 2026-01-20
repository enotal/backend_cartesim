<?php

namespace App\Http\Controllers;

use App\Models\Sim;
use Illuminate\Http\Request;
use App\Http\Resources\SimResource;
use Illuminate\Support\Facades\Validator;

class SimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resource = Sim::with('demande')->with('province')->with('region')->with('anneeacademique')->orderBy('id', 'ASC')->get();
        return SimResource::collection($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero' => "required|string|unique:sims,simnumero",
            'code' => "string|nullable|unique:sims,simcode",
            // 'dateactivation' => "date|nullable",
            // 'dateremise' => "date|nullable",
            // 'datesuspension' => "date|nullable",
            // 'motifsuspension' => "string|nullable", 
            // 'dateretrait' => "date|nullable",
            // 'motifretrait' => "string|nullable", 
            // 'perte' => "string|size:3",
            // 'declarationperte' => "string|nullable",
            'anneeacademique' => "required|numeric",
            // 'region' => "numeric|nullable",
            // 'province' => "numeric|nullable"
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : champ(s) mal renseigné(s) !" . $validator->errors(), 'status' => 201]);
        }

        $resource = Sim::create([
            'simnumero' => $request->numero,
            'simcode' => $request->code,
            // 'simdateactivation' => $request->dateactivation,
            // 'simdateremise' => $request->dateremise,
            // 'simdatesuspension' => $request->datesuspension,
            // 'simmotifsuspension' => $request->motifsuspension,
            // 'simdateretrait' => $request->dateretrait,
            // 'simmotifretrait' => $request->motifretrait,
            // 'simperte' => $request->perte,
            // 'simdeclarationperte' => $request->declarationperte
            'anneeacademique_id' => $request->anneeacademique,
            'demande_id' => $request->demande ? $request->demande : null,
            'province_id' => $request->province ? $request->province : null,
            'region_id' => $request->region ? $request->region : null,
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
        $resource = Sim::with('demande')->with('province')->with('region')->with('anneeacademique')->where('id', $id)->first();
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
        if (! $request->has('switchcase')) {
            $validator = Validator::make($request->all(), [
                'numero' => "required|string|unique:sims,simnumero," . $id,
                'code' => "string|nullable|unique:sims,simcode," . $id,
                // 'dateactivation' => "date|nullable",
                // 'dateremise' => "date|nullable",
                // 'datesuspension' => "date|nullable",
                // 'motifsuspension' => "string|nullable", 
                // 'dateretrait' => "date|nullable",
                // 'motifretrait' => "string|nullable", 
                // 'perte' => "string|size:3",
                // 'declarationperte' => "string|nullable",
                'anneeacademique' => "required|numeric",
                // 'region' => "numeric|nullable",
                // 'province' => "numeric|nullable",
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : champ(s) mal renseigné(s) !", 'status' => 201]);
            }

            $resource = Sim::findOrFail($id)->update([
                'simnumero' => $request->numero,
                'simcode' => $request->code,
                'simdateactivation' => $request->dateactivation,
                'simdateremise' => $request->dateremise,
                'simdatesuspension' => $request->datesuspension,
                'simdateretrait' => $request->dateretrait,
                'simperdue' => $request->perdue,
                'simdeclarationperte' => $request->declarationperte,
                'simcommentaire' => $request->commentaire,
                'anneeacademique_id' => $request->anneeacademique,
                'demande_id' => $request->demande ? $request->demande : null,
                'province_id' => $request->province ? $request->province : null,
                'region_id' => $request->region ? $request->region : null,
            ]);
            if ($resource) {
                return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement édité !", 'status' => 200]);
            }
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : édition non effectuée !", 'status' => 201]);
        } else {
            // Cas particulier : déclaration de perte 
            if ($request->switchcase === "declaresimlost") {
                return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement édité !" . $request->switch, 'status' => 200]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $resource = ($id === "all") ? Sim::truncate() : Sim::findOrFail($id)->delete();

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
            if ($column['name'] === "numéro") {
                $columns['numero'] = $column['value'];
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
            $resource = Sim::where('simnumero', $import[$columns['numero']])->orWhere('simcode', $import[$columns['code']])->first();
            if ($resource) {
                $doublons[$i++] = $resource->repidentifiant;
                $undones[$j++] = $resource->repidentifiant;
            } else {
                $response = Sim::create([
                    'simnumero' => $import[$columns['numero']],
                    'simcode' => $import[$columns['code']],
                    'anneeacademique_id' => $request->anneeacademique
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
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : $count importation(s) effectuée(s) / " .  $total . " !" . $request->anneeacademique, 'data' => [], 'status' => 200]);
        } else {
            // Partiellement, à cause de doublons 
            if ($i > 0) {
                return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : " . $count . " importation(s) effectuée(s) / " . $total . " ! " . count($doublons) . " doublon(s) existants(s) !", 'data' => $doublons, 'status' => 201]);
            }
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : " . $count . " importation(s) effectuée(s) / " . $total . " ! " . count($undones) . " non importé(s) !", 'data' => $undones, 'status' => 202]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function simAttribuerRegion(Request $request)
    {
        $sims = Sim::whereNull('demande_id')->whereNull('region_id')->where('anneeacademique_id', $request->anneeacademique)->get();
        $count = $sims->count();
        if ($count > 0 && $request->nombre <= $count) {

            $c = 0;
            $i = 0;
            while ($i < $request->nombre) {
                $resource = Sim::findOrFail($sims[$i]->id)->update([
                    'region_id' => $request->region,
                ]);
                if ($resource) {
                    $c++;
                }
                $i++;
            }

            if ($c > 0) {
                return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : $c carte(s) sim attribuée(s) / " . $request->nombre . " à la région !", 'status' => 200]);
            }
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : aucune carte sim attribuée à la région !", 'status' => 201]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : aucune carte sim à attribuer ou nombre dépassant la limite !", 'status' => 201]);
    }

    /**
     * Unlink from region.
     */
    public function unlinkRegion(string $id, string $nbr, string $anneeacademique)
    {
        $response = Sim::where('region_id', $id)->where('anneeacademique_id', $anneeacademique)->update([
            'region_id' => null,
        ]);

        if ($response) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : $nbr carte SIM dissociée(s) de la région !", 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : $nbr carte SIM non dissociée(s) de la région !", 'status' => 201]);
    }

    /**
     * Unlink from province.
     */
    public function linkProvince(Request $request)
    {
        $regionSims = Sim::where('anneeacademique_id', $request->anneeacademique)->where('region_id', $request->region)->whereNull('province_id')->whereNull('demande_id')->get();

        $nbr = count($regionSims);
        if ($nbr < 1) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : $nbr carte SIM non associée(s) aux provinces !", 'status' => 201]);
        }

        $count = 0;
        $undone = 0;
        $j = 0;
        foreach ($request->provinces as $key => $value) {
            for ($i = 0; $i < $value; $i++) {
                $response = Sim::findOrFail($regionSims[$j++]->id)->update([
                    'province_id' => $key,
                ]);
                if ($response) {
                    $count++;
                } else {
                    $undone++;
                }
            }
        }

        // 100% !!!
        if ($undone < 1) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : $count carte SIM associée(s) aux provinces !", 'status' => 200]);
        } else {
            return response()->json(['success' => false, 'type' => "warning", 'message' => "Echec : $undone / " . count($request->provinces) . " carte SIM non associée(s) aux provinces !", 'status' => 202]);
        }
    }

    /**
     * Unlink from province.
     */
    public function unlinkProvince(string $id, string $nbr, string $anneeacademique)
    {
        $response = Sim::where('province_id', $id)->where('anneeacademique_id', $anneeacademique)->update([
            'province_id' => null,
        ]);

        if ($response) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : $nbr carte SIM dissociée(s) de la province !", 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : $nbr carte SIM non dissociée(s) de la province !", 'status' => 201]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function simRemise(Request $request)
    {
        $resource = Sim::whereNotNull('demande_id')->whereNull('simdateremise')->get();
        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : carte(s) sim disponible(s) !", 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : aucune carte sim disponible !", 'status' => 201]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function simRemettre(Request $request)
    {
        $today = date('Y-m-d');
        $resource = Sim::findOrFail($request->id)->update([
            'simdateremise' => $today,
        ]);

        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : carte sim remise !", 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : remise non effectuée !", 'status' => 201]);
    }
}
