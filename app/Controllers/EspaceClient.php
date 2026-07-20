<?php

namespace App\Controllers;

use App\Models\Client;
use App\Models\Compte;
use App\Models\Operation;
use App\Models\TypeOperation;
use App\Models\BaremeFrais;

class EspaceClient extends BaseController
{
    private function ensureClient($telephone)
    {
        $client = (new Client())->where('telephone', $telephone)->first();
        if (! $client) {
            $clientId = (new Client())->insert(['telephone' => $telephone], true);
            (new Compte())->insert(['client_id' => $clientId, 'solde' => 0], true);
            $client = (new Client())->find($clientId);
        }
        return $client;
    }

    private function getCompte($clientId)
    {
        return (new Compte())->where('client_id', $clientId)->first();
    }

    private function requireLogin()
    {
        $tel = $this->session->get('telephone');
        if (! $tel) {
            return redirect()->to('client/login');
        }
        return $tel;
    }

    private function operateurNom($telephone)
    {
        $prefixe = (new \App\Models\Prefixe())
            ->select('operateur.nom as nom')
            ->join('operateur', 'operateur.id = prefixe.operateur_id', 'left')
            ->where('prefixe.prefixe', substr($telephone, 0, 3))
            ->first();
        if (! $prefixe) {
            $prefixe = (new \App\Models\Prefixe())
                ->select('operateur.nom as nom')
                ->join('operateur', 'operateur.id = prefixe.operateur_id', 'left')
                ->where('prefixe.prefixe', substr($telephone, 0, 4))
                ->first();
        }
        return $prefixe['nom'] ?? 'Inconnu';
    }

    private function fraisFor($typeNom, $montant)
    {
        $type = (new TypeOperation())->where('nom', $typeNom)->first();
        if (! $type) {
            return 0;
        }
        $bareme = (new BaremeFrais())
            ->where('type_operation_id', $type['id'])
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();
        return $bareme ? (float) $bareme['frais'] : 0;
    }

    private function operateurId($telephone)
    {
        $prefixe = (new \App\Models\Prefixe())
            ->select('operateur.id as id')
            ->join('operateur', 'operateur.id = prefixe.operateur_id', 'left')
            ->where('prefixe.prefixe', substr($telephone, 0, 3))
            ->first();
        if (! $prefixe) {
            $prefixe = (new \App\Models\Prefixe())
                ->select('operateur.id as id')
                ->join('operateur', 'operateur.id = prefixe.operateur_id', 'left')
                ->where('prefixe.prefixe', substr($telephone, 0, 4))
                ->first();
        }
        return $prefixe['id'] ?? null;
    }

    private function commissionFor($srcTel, $destTel, $montant)
    {
        $srcOp = $this->operateurId($srcTel);
        $dstOp = $this->operateurId($destTel);
        if ($srcOp === null || $dstOp === null || $srcOp === $dstOp) {
            return 0;
        }
        $com = (new \App\Models\CommissionInterOperateur())
            ->where('operateur_source_id', $srcOp)
            ->where('operateur_destination_id', $dstOp)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();
        if (! $com) {
            return 0;
        }

        return (float) $montant * ((float) $com['pourcentage'] / 100);
    }

    public function index()
    {
        return redirect()->to('client/login');
    }

    public function login()
    {
        $data = [
            'title'       => 'Connexion client',
            'title_brand' => 'VOLA',
        ];
        return view('client/login', $data);
    }

    public function doLogin()
    {
        $telephone = trim($this->request->getPost('telephone'));
        if (! preg_match('/^[0-9]{9,10}$/', $telephone)) {
            return redirect()->back()->withInput()->with('error', 'Numéro de téléphone invalide.');
        }
        // Block login if phone prefix is not configured
        $prefixe = (new \App\Models\Prefixe())
            ->where('prefixe', substr($telephone, 0, 3))
            ->first();
        if (! $prefixe) {
            $prefixe = (new \App\Models\Prefixe())
                ->where('prefixe', substr($telephone, 0, 4))
                ->first();
        }
        if (! $prefixe) {
            return redirect()->back()->withInput()->with('error', 'Préfixe inconnu.');
        }
        $this->ensureClient($telephone);
        $this->session->set('telephone', $telephone);
        return redirect()->to('client/dashboard');
    }

    public function logout()
    {
        $this->session->remove('telephone');
        return redirect()->to('client/login')->with('success', 'Déconnecté.');
    }

    public function dashboard()
    {
        $tel = $this->requireLogin();
        if ($tel instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $tel;
        }
        $client = $this->ensureClient($tel);
        $compte = $this->getCompte($client['id']);

        $recent = (new Operation())
            ->select('operation.*, type_operation.nom as type')
            ->join('type_operation', 'type_operation.id = operation.type_operation_id')
            ->groupStart()
                ->where('compte_source', $compte['id'])
                ->orWhere('compte_destination', $compte['id'])
            ->groupEnd()
            ->orderBy('date_operation', 'desc')
            ->findAll(5);

        $data = [
            'title'       => 'Tableau de bord',
            'title_brand' => 'VOLA',
            'nav'         => ['Historique' => 'client/historique', 'Déconnexion' => 'client/logout'],
            'telephone'   => $tel,
            'operateur'   => $this->operateurNom($tel),
            'solde'       => $compte['solde'],
            'recent'      => $recent,
            'monCompte'   => $compte['id'],
        ];
        return view('client/dashboard', $data);
    }

    public function solde()
    {
        $tel = $this->requireLogin();
        if ($tel instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $tel;
        }
        $client = $this->ensureClient($tel);
        $compte = $this->getCompte($client['id']);

        $data = [
            'title'       => 'Mon solde',
            'title_brand' => 'VOLA',
            'nav'         => ['Tableau de bord' => 'client/dashboard', 'Déconnexion' => 'client/logout'],
            'telephone'   => $tel,
            'solde'       => $compte['solde'],
        ];
        return view('client/solde', $data);
    }

    public function depot()
    {
        $tel = $this->requireLogin();
        if ($tel instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $tel;
        }
        $data = [
            'title'       => 'Dépôt',
            'title_brand' => 'VOLA',
            'nav'         => ['Tableau de bord' => 'client/dashboard', 'Déconnexion' => 'client/logout'],
            'telephone'   => $tel,
        ];
        return view('client/depot', $data);
    }

    public function doDepot()
    {
        $tel = $this->requireLogin();
        if ($tel instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $tel;
        }
        $montant = (float) $this->request->getPost('montant');
        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }
        $client  = $this->ensureClient($tel);
        $compte  = $this->getCompte($client['id']);
        $frais   = $this->fraisFor('dépôt', $montant);

        $compteModel = new Compte();
        $compteModel->update($compte['id'], ['solde' => $compte['solde'] + $montant]);

        $type = (new TypeOperation())->where('nom', 'dépôt')->first();
        (new Operation())->insert([
            'type_operation_id' => $type['id'],
            'compte_source'     => $compte['id'],
            'compte_destination' => $compte['id'],
            'montant'           => $montant,
            'frais'             => $frais,
        ]);

        return redirect()->to('client/dashboard')->with('success', 'Dépôt de ' . number_format($montant, 2, ',', ' ') . ' Ar effectué.');
    }

    public function retrait()
    {
        $tel = $this->requireLogin();
        if ($tel instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $tel;
        }
        $compte = $this->getCompte($this->ensureClient($tel)['id']);
        $data = [
            'title'       => 'Retrait',
            'title_brand' => 'VOLA',
            'nav'         => ['Tableau de bord' => 'client/dashboard', 'Déconnexion' => 'client/logout'],
            'telephone'   => $tel,
            'solde'       => $compte['solde'],
        ];
        return view('client/retrait', $data);
    }

    public function doRetrait()
    {
        $tel = $this->requireLogin();
        if ($tel instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $tel;
        }
        $montant = (float) $this->request->getPost('montant');
        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }
        $client = $this->ensureClient($tel);
        $compte = $this->getCompte($client['id']);
        $frais  = $this->fraisFor('retrait', $montant);

        if ($compte['solde'] < ($montant + $frais)) {
            return redirect()->back()->with('error', 'Solde insuffisant (montant + frais).');
        }

        $compteModel = new Compte();
        $compteModel->update($compte['id'], ['solde' => $compte['solde'] - $montant - $frais]);

        $type = (new TypeOperation())->where('nom', 'retrait')->first();
        (new Operation())->insert([
            'type_operation_id'  => $type['id'],
            'compte_source'      => $compte['id'],
            'compte_destination' => null,
            'montant'            => $montant,
            'frais'              => $frais,
        ]);

        return redirect()->to('client/dashboard')->with('success', 'Retrait de ' . number_format($montant, 2, ',', ' ') . ' Ar effectué.');
    }

    public function transfert()
    {
        $tel = $this->requireLogin();
        if ($tel instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $tel;
        }
        $data = [
            'title'       => 'Transfert',
            'title_brand' => 'VOLA',
            'nav'         => ['Tableau de bord' => 'client/dashboard', 'Déconnexion' => 'client/logout'],
            'telephone'   => $tel,
        ];
        return view('client/transfert', $data);
    }

    public function frais()
    {
        $tel = $this->requireLogin();
        if ($tel instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $tel;
        }
        $type      = $this->request->getGet('type');
        $montant   = (float) $this->request->getGet('montant');
        $dest      = $this->request->getGet('dest');
        $inclure   = $this->request->getGet('inclure_frais_retrait') === '1';
        $nbDest    = max(1, (int) $this->request->getGet('nb_dest', 1));
        $frais     = $this->fraisFor($type, $montant);
        $destNom   = null;
        $destOperateur = null;

        if ($dest) {
            $c = (new Client())->where('telephone', $dest)->first();
            if ($c) {
                $destNom = $c['nom'] ?? null;
            }
            $destOperateur = $this->operateurNom($dest);
        }

        $commission = ($dest && $type === 'transfert')
            ? $this->commissionFor($tel, $dest, $montant)
            : 0;

        $fraisRetraitSupplementaire = 0;
        if ($inclure && $type === 'transfert' && $dest) {
            $fraisRetraitSupplementaire = $this->fraisFor('retrait', $montant) * $nbDest;
        }

        $total = $montant + $frais + $commission + $fraisRetraitSupplementaire;

        return $this->response->setJSON([
            'frais'      => $frais,
            'commission' => $commission,
            'total'      => $total,
            'destNom'    => $destNom,
            'destOperateur' => $destOperateur,
            'srcOperateur'  => $this->operateurNom($tel),
            'fraisRetraitSupplementaire' => $fraisRetraitSupplementaire,
        ]);
    }

    public function doTransfert()
    {
        $tel = $this->requireLogin();
        if ($tel instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $tel;
        }

        $destinataires = trim($this->request->getPost('destinataires'));
        $montantTotal  = (float) $this->request->getPost('montant');
        $inclureFrais  = $this->request->getPost('inclure_frais_retrait') === '1';

        $liste = array_filter(array_map('trim', explode("\n", $destinataires)), fn($n) => $n !== '');

        if (empty($liste)) {
            return redirect()->back()->with('error', 'Veuillez saisir au moins un numéro de destinataire.');
        }

        $liste = array_values(array_unique($liste));
        $nbDest = count($liste);

        foreach ($liste as $destTel) {
            if (! preg_match('/^[0-9]{9,10}$/', $destTel)) {
                return redirect()->back()->with('error', 'Numéro invalide : ' . esc($destTel));
            }
            if ($destTel === $tel) {
                return redirect()->back()->with('error', 'Vous ne pouvez pas vous transférer à vous-même.');
            }
        }

        if ($montantTotal <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        $montantParDest = $montantTotal / $nbDest;

        $srcClient = $this->ensureClient($tel);
        $srcCompte = $this->getCompte($srcClient['id']);

        $fraisTotal         = $this->fraisFor('transfert', $montantTotal);
        $commissionTotal    = 0;
        foreach ($liste as $destTel) {
            $commissionTotal += $this->commissionFor($tel, $destTel, $montantParDest);
        }

        $fraisRetraitSupplementaire = 0;
        if ($inclureFrais) {
            foreach ($liste as $destTel) {
                $fraisRetraitSupplementaire += $this->fraisFor('retrait', $montantParDest);
            }
        }

        $totalADebiter = $montantTotal + $fraisTotal + $commissionTotal + $fraisRetraitSupplementaire;

        if ($srcCompte['solde'] < $totalADebiter) {
            return redirect()->back()->with('error', 'Solde insuffisant (montant + frais + commission' . ($inclureFrais ? ' + frais retrait inclus' : '') . ').');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $db->table('compte')->where('id', $srcCompte['id'])->update(['solde' => $srcCompte['solde'] - $totalADebiter]);

        $type = (new TypeOperation())->where('nom', 'transfert')->first();

        foreach ($liste as $destTel) {
            $destClient = $this->ensureClient($destTel);
            $destCompte = $this->getCompte($destClient['id']);

            $db->table('compte')->where('id', $destCompte['id'])->update(['solde' => $destCompte['solde'] + $montantParDest]);

            $db->table('operation')->insert([
                'type_operation_id'    => $type['id'],
                'compte_source'        => $srcCompte['id'],
                'compte_destination'   => $destCompte['id'],
                'montant'              => $montantParDest,
                'frais'                => $fraisTotal / $nbDest,
                'commission'           => $commissionTotal / $nbDest,
                'inclure_frais_retrait'=> $inclureFrais ? 1 : 0,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Erreur lors du transfert. Veuillez réessayer.');
        }

        $succesMsg = 'Transfert de ' . number_format($montantTotal, 2, ',', ' ') . ' Ar vers ' . $nbDest . ' destinataire(s) effectué.';
        return redirect()->to('client/dashboard')->with('success', $succesMsg);
    }

    public function historique()
    {
        $tel = $this->requireLogin();
        if ($tel instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $tel;
        }
        $client = $this->ensureClient($tel);
        $compte = $this->getCompte($client['id']);

        $ops = (new Operation())
            ->select('operation.*, type_operation.nom as type')
            ->join('type_operation', 'type_operation.id = operation.type_operation_id')
            ->groupStart()
                ->where('compte_source', $compte['id'])
                ->orWhere('compte_destination', $compte['id'])
            ->groupEnd()
            ->orderBy('date_operation', 'desc')
            ->findAll();

        $data = [
            'title'       => 'Historique',
            'title_brand' => 'VOLA',
            'nav'         => ['Tableau de bord' => 'client/dashboard', 'Déconnexion' => 'client/logout'],
            'telephone'   => $tel,
            'operations'  => $ops,
            'monCompte'   => $compte['id'],
        ];
        return view('client/historique', $data);
    }
}
