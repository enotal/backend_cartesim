<?php

namespace App\Http\Controllers;

use App\Models\Sim;
use App\Models\Demande;
use App\Models\Repondant;
use Illuminate\Http\Request;
use App\Mail\DemandeGuestSoumettre;
use Illuminate\Support\Facades\Mail;

use App\Http\Resources\DemandeResource;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\Validator;

class DemandeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resource = Demande::with('repondant')->with('sessiondemande')->with('sessionremise')->with('site')->with('sim')->with('user')->orderBy('id', 'ASC')->get();
        return DemandeResource::collection($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'code' => "required|string|unique:demandes,dmdcode",
            'date' => "required|date",
            'commentaire' => "string|nullable",
            'identifiant' => "required|string",
            'sessiondemande' => "required|numeric",
            'site' => "required|numeric"
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : un champ est obligatoire !", 'status' => 201]);
        }

        $resource = Repondant::with('demandes')->with('demandes.sim')->where('repidentifiant', $request->identifiant)->get();

        if ($resource) {
            $demandCount = $resource->demandes->count();
            // Aucune demande soumise !
            if ($demandCount < 1) {
                // Enregistrement de la demande 
                $today = $request->has('date') ? $request->date : date('Y-m-d');
                $code = substr(sha1($today . time()), 0, 12);
                $response = Demande::create([
                    'dmdcode' => $code,
                    'dmddate' => $today,
                    'dmdcommentaire' => $request->commentaire,
                    'repondant_id' => $resource->id,
                    'sessiondemande_id' => $request->sessiondemande,
                    'site_id' => $request->site
                ]);
                // 
                if ($response) {
                    // Envoi de la notification par mail 
                    // Méthode 1 :  
                    // Mail::to($request->user())->cc($moreUsers)->bcc($evenMoreUsers)->locale('es')->send(new OrderShipped($order));
                    // Méthode 2 : Queueing a Mail Message 
                    // Mail::to($request->user())->cc($moreUsers)->bcc($evenMoreUsers)->queue(new OrderShipped($order));
                    // Méthode 3 : Delayed Message Queueing 
                    // Mail::to($request->user())->cc($moreUsers)->bcc($evenMoreUsers)->later(now()->plus(minutes: 10), new OrderShipped($order));
                    $order = "Test carte sim";
                    // Mail::to($request->email)->cc('enotal12@yahoo.fr')->bcc('aime.tone@uv.bf')->locale('fr')->send(new DemandeGuestSoumettre($order));

                    return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : Votre demande a bien été soumise !\n Une notification vous a été envoyée à l'adresse [ " . $resource->repemail . " ] !".$response, "data" => $resource, 'status' => 200]);
                }
                return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : Votre demande n'a pas pu être soumise !\n Merci d'essayer à nouveau !", "data" => $resource, 'status' => 200]);
            } else {
                // Au moins une demande soumise !
                $simCount = $resource->demandes->sim->count();
                $demand = $resource->demandes[$demandCount - 1];
                if ($simCount < 1) {
                    return response()->json(['success' => false, 'type' => "warning", 'message' => "Echec : Vous avez déjà une demande en cours : " . $demand->dmdcode . " !", "data" => $resource, 'status' => 200]);
                }
                return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : Vous êtes déjà bénéficiaire de ".$simCount." carte(s) SIM !", "data" => $demand, 'status' => 200]);
            }
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : Vous n'êtes pas autorisé(e) à soumettre une demande !\n Merci de contacter les services compétents !", 'status' => 201]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $resource = Demande::with('repondant')->with('sessiondemande')->with('sessionremise')->with('site')->with('sim')->with('user')->first();
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
            // 'code' => "required|string|unique:demandes,dmdcode," . $id,
            'date' => "required|date",
            'commentaire' => "string|nullable",
            // 'repondant' => "required|numeric",
            'sessiondemande' => "required|numeric",
            'site' => "required|numeric"
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : champ(s) mal renseigné(s) !", 'status' => 201]);
        }

        $today = $request->has('date') ? $request->date : date('Y-m-d'); // Formats the date as YYYY-MM-DD

        $resource = Demande::findOrFail($id)->update([
            // 'dmdcode' => $request->code,
            'dmddate' => $request->date ? $request->date : $today,
            'dmdcommentaire' => $request->commentaire,
            // 'repondant_id' => $request->repondant,
            'sessiondemande_id' => $request->sessiondemande,
            'site_id' => $request->site
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
        $resource = ($id === "all") ? Demande::truncate() : Demande::findOrFail($id)->delete();

        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : enregistrement supprimé !", 'status' => 200]);
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : suppression non effectuée !", 'status' => 201]);
    }

    /**
     * Store a newly created resource in storage.
     * Soumission par l'étudiant
     */
    public function storeGuestSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifiant' => "required|string",
            'sessiondemande' => "required|numeric",
            'site' => "required|numeric"
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : un champ est obligatoire !", 'status' => 201]);
        }

        $resource = Repondant::with('demandes')->with('demandes.sim')->where('repidentifiant', $request->identifiant)->first();

        if ($resource) {
            $demandCount = $resource->demandes->count();
            // Aucune demande soumise !
            if ($demandCount < 1) {
                // Enregistrement de la demande 
                $today = $request->has('date') ? $request->date : date('Y-m-d');
                $code = substr(sha1($today . time()), 0, 12);
                $response = Demande::create([
                    'dmdcode' => $code,
                    'dmddate' => $today,
                    // 'dmdcommentaire' => $request->commentaire,
                    'repondant_id' => $resource->id,
                    'sessiondemande_id' => $request->sessiondemande,
                    'site_id' => $request->site
                ]);
                // 
                if ($response) {
                    // Envoi de la notification par mail 
                    // Méthode 1 :  
                    // Mail::to($request->user())->cc($moreUsers)->bcc($evenMoreUsers)->locale('es')->send(new OrderShipped($order));
                    // Méthode 2 : Queueing a Mail Message 
                    // Mail::to($request->user())->cc($moreUsers)->bcc($evenMoreUsers)->queue(new OrderShipped($order));
                    // Méthode 3 : Delayed Message Queueing 
                    // Mail::to($request->user())->cc($moreUsers)->bcc($evenMoreUsers)->later(now()->plus(minutes: 10), new OrderShipped($order));
                    $order = "Test cartesim";
                    Mail::to($request->email)->cc('enotal12@yahoo.fr')->bcc('aime.tone@uv.bf')->locale('fr')->send(new DemandeGuestSoumettre($order));

                    return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : Votre demande a bien été soumise !\n Une notification vous a été envoyée à l'adresse [ " . $resource->repemail . " ] !", "data" => $resource, 'status' => 200]);
                }
                return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : Votre demande n'a pas pu être soumise !\n Merci d'essayer à nouveau !", "data" => $resource, 'status' => 200]);
            } else {
                // Au moins une demande soumise !
                $simCount = $resource->demandes->sim->count();
                $demand = $resource->demandes[$demandCount - 1];
                if ($simCount < 1) {
                    return response()->json(['success' => false, 'type' => "warning", 'message' => "Echec : Vous avez déjà une demande en cours : " . $demand->dmdcode . " !", "data" => $resource, 'status' => 200]);
                }
                return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : Vous êtes déjà une carte SIM !", "data" => $demand, 'status' => 200]);
            }
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : Vous n'êtes pas autorisé(e) à soumettre une demande !\n Merci de contacter les services compétents !", 'status' => 201]);
    }

    /**
     * Display the specified resource.
     */
    public function simDeclarerPerte(Request $request)
    {
        $resource = Demande::with('repondant')->with('sim')->where('dmdcode', $request->code)->first();

        if ($resource) {
            if (!is_null($resource->sim->demande_id)) {
                return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : 1 enregistrement trouvé !", "data" => $resource, 'status' => 200]);
            }
            return response()->json(['success' => false, 'type' => "warning", 'message' => "Erreur : Vous ne pouvez pas déclarer de perte car aucune carte SIM ne vous a été attribuée !", "data" => $resource, 'status' => 201]);
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : Aucune demande ne correspond au code fourni !", 'status' => 201]);
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
            $resource = Demande::with('repondant')->with('sessiondemande')->with('sessionremise')->with('site')->with('sim')->with('user')->where('dmd' . $keys[0], $request[$keys[0]])->first();
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
    public function demandeAssignable(Request $request)
    {
        $resource = Demande::with('repondant')->with('sim')->where('dmdcode', $request->code)->first();

        if ($resource) {
            if ($resource->sim) {
                return response()->json(['success' => false, 'type' => "danger", 'message' => "Erreur : demande associée à une carte SIM !", 'status' => 201]);
            }
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : 1 enregistrement trouvé !", "data" => $resource, 'status' => 200]);
        }

        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : aucune demande trouvée !", 'status' => 201]);
    }

    /**
     * Display the specified resource.
     */
    public function demandeAttribuerSim(Request $request)
    {
        if ($request->attribute === "unique") {
            $demande = Demande::findOrFail($request->demande);
            // année académique, région, province du site désiré pour la remise de la carte sim
            $sim = Sim::where('anneeacademique_id', $request->anneeacademique)->whereNull('demande_id')->whereNotNull('province_id')->first();
            if ($sim) {
                $resource = $sim->update([
                    'demande_id' => $request->demande,
                ]);

                if ($resource) {
                    return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : attribution effectuée !", 'status' => 200]);
                }
                return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : carte SIM non attribuée !", 'status' => 201]);
            } else {
                return response()->json(['success' => false, 'type' => "warning", 'message' => "Erreur : aucune carte SIM disponible !", 'status' => 201]);
            }
        }

        if ($request->attribute === "multiple") {
            // 

        }
    }

    public function demandeDissocierSim(Request $request)
    {
        $resource = Sim::where('demande_id', $request->demande)->update([
            'demande_id' => null,
        ]);

        if ($resource) {
            return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : dissociation effectuée !", 'status' => 200]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : carte SIM non dissocier !", 'status' => 201]);
    }

    /**
     * Display the specified resource.
     */
    public function getCurrent()
    {
        $anneeacademique = AnneeacademiqueController::getCurrent('resource');
        if ($anneeacademique) {
            $resource = Demande::where('dmddate', '>=', $anneeacademique->acadatedebut)->where('dmddate', '<=', $anneeacademique->acadatefin)->first();
            if ($resource) {
                return response()->json(['success' => true, 'type' => "success", 'message' => "Succès : 1 enregistrement trouvé !", "data" => $resource, 'status' => 200]);
            }
            return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : aucun enregistrement correspondant !", "data" => $resource, 'status' => 201]);
        }
        return response()->json(['success' => false, 'type' => "danger", 'message' => "Echec : aucun enregistrement correspondant !", "data" => $anneeacademique, 'status' => 201]);
    }
}
