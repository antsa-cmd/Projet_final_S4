<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\Operateur;
use App\Models\TypeOperation;
use App\Models\BaremeFrais;

class InitSeeder extends Seeder
{
    public function run()
    {
        // Opérateurs
        $operateur = new Operateur();
        $ops = ['Telma', 'Orange', 'Airtel'];
        $opIds = [];
        foreach ($ops as $nom) {
            if (! $operateur->where('nom', $nom)->first()) {
                $opIds[$nom] = $operateur->insert(['nom' => $nom], true);
            } else {
                $opIds[$nom] = $operateur->where('nom', $nom)->first()['id'];
            }
        }

        // Préfixes
        $prefixes = [
            '033' => 'Telma', '034' => 'Telma', '038' => 'Telma',
            '032' => 'Orange', '0331' => 'Orange',
            '031' => 'Airtel', '0321' => 'Airtel',
        ];
        $prefixeModel = new \App\Models\Prefixe();
        foreach ($prefixes as $p => $op) {
            if (! $prefixeModel->where('prefixe', $p)->first()) {
                $prefixeModel->insert(['prefixe' => $p, 'operateur_id' => $opIds[$op]]);
            }
        }

        // Types d'opération
        $type = new TypeOperation();
        $types = ['dépôt', 'retrait', 'transfert'];
        $typeIds = [];
        foreach ($types as $nom) {
            if (! $type->where('nom', $nom)->first()) {
                $typeIds[$nom] = $type->insert(['nom' => $nom], true);
            } else {
                $typeIds[$nom] = $type->where('nom', $nom)->first()['id'];
            }
        }

        // Barèmes de frais par tranche (modifiable via l'interface)
        $bareme = new BaremeFrais();
        $tranches = [
            [100, 1000, 50],
            [1001, 5000, 50],
            [5001, 10000, 50],
            [10001, 25000, 100],
            [25001, 50000, 200],
            [50001, 100000, 400],
            [100001, 250000, 800],
            [250001, 500000, 1500],
            [500001, 1000000, 1500],
            [1000001, 2000000, 2500],
            [2000001, 5000000, 3000],
        ];
        foreach (['dépôt', 'retrait', 'transfert'] as $t) {
            foreach ($tranches as $tr) {
                $exists = $bareme->where('type_operation_id', $typeIds[$t])
                                 ->where('montant_min', $tr[0])
                                 ->where('montant_max', $tr[1])
                                 ->first();
                if (! $exists) {
                    $bareme->insert([
                        'type_operation_id' => $typeIds[$t],
                        'montant_min'       => $tr[0],
                        'montant_max'       => $tr[1],
                        'frais'             => $tr[2],
                    ]);
                }
            }
        }

        // Commission inter-opérateur (pourcentage prélevé en plus des frais de transfert
        // lors d'un envoi vers un autre opérateur). 10% par défaut par tranche.
        $commissionModel = new \App\Models\CommissionInterOperateur();
        foreach ($ops as $src) {
            foreach ($ops as $dst) {
                if ($src === $dst) {
                    continue;
                }
                foreach ($tranches as $tr) {
                    $exists = $commissionModel
                        ->where('operateur_source_id', $opIds[$src])
                        ->where('operateur_destination_id', $opIds[$dst])
                        ->where('montant_min', $tr[0])
                        ->where('montant_max', $tr[1])
                        ->first();
                    if (! $exists) {
                        $commissionModel->insert([
                            'operateur_source_id'      => $opIds[$src],
                            'operateur_destination_id' => $opIds[$dst],
                            'montant_min'              => $tr[0],
                            'montant_max'              => $tr[1],
                            'pourcentage'              => 10,
                        ]);
                    }
                }
            }
        }
        // Paramètre : opérateur de l'application (ex: Telma)
        $param = new \App\Models\Parametre();
        if (! $param->where('cle', 'operateur_application_id')->first()) {
            $param->insert([
                'cle'   => 'operateur_application_id',
                'valeur' => $opIds['Telma'],
            ]);
        }
    }
}
