<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Site;
use App\Models\User;
use App\Models\Region;
use App\Models\Province;
use App\Models\Sessionremise;
use App\Models\Typerepondant;
use App\Models\Sessiondemande;
use App\Models\Anneeacademique;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::truncate();
        User::truncate();
        Typerepondant::truncate();
        Region::truncate();
        Province::truncate();
        Site::truncate();
        Anneeacademique::truncate();
        Sessiondemande::truncate();
        Sessionremise::truncate();

        //=== Rôles 

        // $table->enum('role', ["étudiant", "agent", "agent-bcmp", "agent-daf", "bcmp", "daf", "hiérarchie", "administrateur", "ENT"])->default("agent");
        $roles = [
            [
                // 'id' => 1,
                'libelle' => "point focal",
                'description' => null,
                'active' => "oui"
            ],
            [
                // 'id' => 2,
                'libelle' => "chargé de remise",
                'description' => null,
                'active' => "oui"
            ],
            [
                // 'id' => 3,
                'libelle' => "agent",
                'description' => null,
                'active' => "oui"
            ],
            [
                // 'id' => 4,
                'libelle' => "agent-bcmp",
                'description' => null,
                'active' => "oui"
            ],
            [
                // 'id' => 5,
                'libelle' => "agent-daf",
                'description' => null,
                'active' => "oui"
            ],
            [
                // 'id' => 6,
                'libelle' => "bcmp",
                'description' => null,
                'active' => "oui"
            ],
            [
                // 'id' => 7,
                'libelle' => "daf",
                'description' => null,
                'active' => "oui"
            ],
            [
                // 'id' => 8,
                'libelle' => "hiérarchie",
                'description' => null,
                'active' => "oui"
            ],
            [
                // 'id' => 9,
                'libelle' => "administrateur",
                'description' => null,
                'active' => "oui"
            ],
        ];

        foreach ($roles as $role) {
            Role::create([
                'rlelibelle' => $role['libelle'],
                'rledescription' => $role['description'],
                'rleactive' => $role['active']
            ]);
        }

        // ===

        // User::factory(10)->create();
        $user = User::factory()->create([
            'name' => 'Admin',
            'lastname' => "Admin",
            'email' => 'admin@uv.bf',
            'sexe' => "Masculin",
            'active' => "oui",
            'status' => "non",
        ]);
        $user->roles()->attach(9);

        //=== Types de répondants 

        $typerepondants = [
            [
                'code' => "etd",
                'libelle' => "étudiant",
                'active' => "oui"
            ],
            [
                'code' => "ens",
                'libelle' => "enseignant",
                'active' => "oui"
            ],
            [
                'code' => "atos",
                'libelle' => "personnel ATOS",
                'active' => "oui"
            ]
        ];

        foreach ($typerepondants as $typerepondant) {
            Typerepondant::create([
                'tyrcode' => $typerepondant['code'],
                'tyrlibelle' => $typerepondant['libelle'],
                'tyractive' => $typerepondant['active']
            ]);
        }
        /*
        $tyrs = Typerepondant::all();

        // ===

        // === Régions 

        $regions = [
            [
                // 'id' => 1,
                'code' => null,
                'nom' => "sourou",
                'cheflieu' => "tougan",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 2,
                'code' => null,
                'nom' => "bankui",
                'cheflieu' => "dédougou",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 3,
                'code' => null,
                'nom' => "guiriko",
                'cheflieu' => "bobo-dioulasso",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 4,
                'code' => null,
                'nom' => "tannounyan",
                'cheflieu' => "banfora",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 5,
                'code' => null,
                'nom' => "djoro",
                'cheflieu' => "gaoua",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 6,
                'code' => null,
                'nom' => "yaagda",
                'cheflieu' => "ouahigouya",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 7,
                'code' => null,
                'nom' => "nando",
                'cheflieu' => "koudougou",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 8,
                'code' => null,
                'nom' => "soum",
                'cheflieu' => "djibo",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 9,
                'code' => null,
                'nom' => "kuilsé",
                'cheflieu' => "kaya",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 10,
                'code' => null,
                'nom' => "oubri",
                'cheflieu' => "ziniaré",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 11,
                'code' => null,
                'nom' => "kadiogo",
                'cheflieu' => "ouagadougou",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 12,
                'code' => null,
                'nom' => "nazinon",
                'cheflieu' => "manga",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 13,
                'code' => null,
                'nom' => "nakambé",
                'cheflieu' => "tenkodogo",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 14,
                'code' => null,
                'nom' => "liptako",
                'cheflieu' => "dori",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 15,
                'code' => null,
                'nom' => "goulmou",
                'cheflieu' => "fada n'gourma",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 16,
                'code' => null,
                'nom' => "tapoa",
                'cheflieu' => "diapaga",
                'active' => "oui",
                'commentaire' => null,
            ],
            [
                // 'id' => 17,
                'code' => null,
                'nom' => "sirba",
                'cheflieu' => "bogandé",
                'active' => "oui",
                'commentaire' => null,
            ],
        ];

        foreach ($regions as $region) {
            Region::create([
                'rgncode' => $region['code'],
                'rgnnom' => $region['nom'],
                'rgncheflieu' => $region['cheflieu'],
                'rgnactive' => $region['active'],
                'rgncommentaire' => $region['commentaire'],
            ]);
        }

        // === 

        // === Provinces 

        $province = Province::create([
            'prvcode' => "kdg",
            'prvnom' => "kadiogo",
            'prvcheflieu' => "Ouagadougou",
            'prvactive' => "oui",
            'prvcommentaire' => null,
            'region_id' => 11,
        ]);

        // ===

        // === Sites

        $sites = [
            [
                'libelle' => "ENO Karpala, Ouagadougou",
                'commentaire' => null,
                'active' => "oui",
                'province' => $province->id,
            ],
            [
                'libelle' => "Siège UV-BF, Ouagadougou",
                'commentaire' => null,
                'active' => "oui",
                'province' => $province->id,
            ]
        ];

        foreach ($sites as $site) {
            Site::create([
                'sitlibelle' => $site['libelle'],
                'sitcommentaire' => $site['commentaire'],
                'sitactive' => $site['active'],
                'province_id' => $site['province'], 
            ]);
        }

        // === 

        // === Années académiques

        $aca = Anneeacademique::create([
            'acacode' => "2025-2026",
            'acadatedebut' => "2025-10-01",
            'acadatefin' => "2026-07-31",
            'acaactive' => "oui",
        ]);

        // ===

        // === Sessions de demandes

        Sessiondemande::create([
            'seddatedebut' => "2026-01-15",
            'seddatefin' => "2026-01-31",
            'sedactive' => "oui",
            'anneeacademique_id' => $aca->id,
            'typerepondant_id' => $tyrs[0]->id
        ]);

        // ===

        // Sessions de remises

        Sessionremise::create([
            'serdatedebut' => "2026-02-01",
            'serdatefin' => "2026-02-15",
            'seractive' => "non",
            'anneeacademique_id' => $aca->id,
            'typerepondant_id' => $tyrs[0]->id
        ]);

        // === */
    }
}
