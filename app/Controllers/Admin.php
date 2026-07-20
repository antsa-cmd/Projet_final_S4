<?php

namespace App\Controllers;

use App\Models\Operateur as OperateurModel;
use App\Models\Prefixe;
use App\Models\TypeOperation;
use App\Models\BaremeFrais;
use App\Models\Compte;
use App\Models\Operation;

class Admin extends BaseController
{
    private function nav($active = '')
    {
        $items = [
            ['label' => 'Historique', 'url' => 'admin', 'icon' => 'layout-dashboard'],
            ['label' => 'Opérateurs', 'url' => 'admin/operateurs', 'icon' => 'building-2'],
            ['label' => 'Préfixes', 'url' => 'admin/prefixes', 'icon' => 'hash'],
            ['label' => 'Types', 'url' => 'admin/types', 'icon' => 'tags'],
            ['label' => 'Barèmes', 'url' => 'admin/baremes', 'icon' => 'sliders-horizontal'],
            ['label' => 'Commissions', 'url' => 'admin/commissions', 'icon' => 'arrow-left-right'],
            ['label' => 'Gains', 'url' => 'admin/gains', 'icon' => 'trending-up'],
            ['label' => 'Comptes', 'url' => 'admin/comptes', 'icon' => 'users'],
        ];
        foreach ($items as &$it) {
            $it['active'] = ($it['url'] === $active);
        }
        return $items;
    }

    public function index()
    {
        return $this->dashboard();
    }

    // ---------- DASHBOARD ----------
    public function dashboard()
    {
        $compte  = new Compte();
        $operation = new Operation();
        $client  = new \App\Models\Client();

        $nbClients = $client->countAll();
        $nbOps     = $operation->countAll();
        $totalSolde = $compte->selectSum('solde')->first()['solde'] ?? 0;

        $gains = $operation->selectSum('frais')->first()['frais'] ?? 0;

        $byType = $operation->select('type_operation.nom as type, COUNT(*) as nb, SUM(operation.frais) as gain')
            ->join('type_operation', 'type_operation.id = operation.type_operation_id')
            ->groupBy('type_operation.nom')->findAll();

        $repartition = [];
        $evolution = [];
        $labels = [];
        if (! empty($byType)) {
            foreach ($byType as $r) {
                $repartition[$r['type']] = (int) $r['nb'];
            }
        }
        $days = $operation->select("date(date_operation) as d, COUNT(*) as nb")
            ->groupBy("date(date_operation)")->orderBy('d', 'asc')->findAll(14);
        foreach ($days as $d) {
            $labels[] = date('d/m', strtotime($d['d']));
            $evolution[] = (int) $d['nb'];
        }
        if (empty($labels)) { $labels = ['Aucune']; $evolution = [0]; }

        $recent = $operation->select('operation.*, type_operation.nom as type, client.telephone')
            ->join('type_operation', 'type_operation.id = operation.type_operation_id')
            ->join('compte', 'compte.id = operation.compte_source', 'left')
            ->join('client', 'client.id = compte.client_id', 'left')
            ->orderBy('operation.date_operation', 'desc')->findAll(6);

        $data = [
            'title'       => 'Tableau de bord',
            'title_brand' => 'Espace Opérateur',
            'sidebar'     => true,
            'nav'         => $this->nav('admin'),
            'nbClients'   => $nbClients,
            'nbOps'       => $nbOps,
            'totalSolde'  => $totalSolde,
            'gains'       => $gains,
            'repartition' => $repartition,
            'labels'      => $labels,
            'evolution'   => $evolution,
            'recent'      => $recent,
        ];
        return view('admin/dashboard', $data);
    }

    // ---------- OPERATEURS ----------
    public function operateurs()
    {
        $op = new OperateurModel();
        $operateurs = $op->findAll();
        $prefixe = new Prefixe();
        foreach ($operateurs as &$o) {
            $o['nb_prefixes'] = $prefixe->where('operateur_id', $o['id'])->countAllResults();
        }
        $data = [
            'title'       => 'Opérateurs',
            'title_brand' => 'Espace Opérateur',
            'sidebar'     => true,
            'nav'         => $this->nav('admin/operateurs'),
            'operateurs'  => $operateurs,
        ];
        return view('admin/operateurs', $data);
    }

    // ---------- PREFIXES ----------
    public function prefixes()
    {
        $prefixe = new Prefixe();
        $data = [
            'title'       => 'Préfixes',
            'title_brand' => 'Espace Opérateur',
            'sidebar'     => true,
            'nav'         => $this->nav('admin/prefixes'),
            'prefixes'    => $prefixe->select('prefixe.*, operateur.nom as operateur')
                                     ->join('operateur', 'operateur.id = prefixe.operateur_id', 'left')
                                     ->orderBy('prefixe.prefixe', 'asc')
                                     ->findAll(),
            'operateurs'  => (new OperateurModel())->orderBy('nom')->findAll(),
        ];
        return view('admin/prefixes', $data);
    }

    public function prefixeCreate()
    {
        $prefixe = new Prefixe();
        $rules = [
            'prefixe'     => 'required|is_unique[prefixe.prefixe]',
            'operateur_id' => 'required|integer',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Préfixe invalide ou déjà existant.');
        }
        $prefixe->insert($this->request->getPost());
        return redirect()->to('admin/prefixes')->with('success', 'Préfixe ajouté.');
    }

    public function prefixeDelete($id)
    {
        (new Prefixe())->delete($id);
        return redirect()->to('admin/prefixes')->with('success', 'Préfixe supprimé.');
    }

    public function operateurCreate()
    {
        $op = new OperateurModel();
        if (! $this->validate(['nom' => 'required|is_unique[operateur.nom]'])) {
            return redirect()->back()->with('error', 'Nom d\'opérateur invalide ou existant.');
        }
        $op->insert(['nom' => $this->request->getPost('nom')]);
        return redirect()->to('admin/operateurs')->with('success', 'Opérateur ajouté.');
    }

    public function operateurDelete($id)
    {
        (new OperateurModel())->delete($id);
        return redirect()->to('admin/operateurs')->with('success', 'Opérateur supprimé.');
    }

    // ---------- TYPES D'OPERATION ----------
    public function types()
    {
        $data = [
            'title'       => 'Types d\'opération',
            'title_brand' => 'Espace Opérateur',
            'sidebar'     => true,
            'nav'         => $this->nav('admin/types'),
            'types'       => (new TypeOperation())->orderBy('nom')->findAll(),
        ];
        return view('admin/types', $data);
    }

    public function typeCreate()
    {
        $type = new TypeOperation();
        if (! $this->validate(['nom' => 'required|is_unique[type_operation.nom]'])) {
            return redirect()->back()->with('error', 'Nom de type invalide ou existant.');
        }
        $type->insert(['nom' => $this->request->getPost('nom')]);
        return redirect()->to('admin/types')->with('success', 'Type d\'opération ajouté.');
    }

    public function typeDelete($id)
    {
        (new TypeOperation())->delete($id);
        return redirect()->to('admin/types')->with('success', 'Type d\'opération supprimé.');
    }

    // ---------- BAREME DES FRAIS ----------
    public function baremes()
    {
        $bareme = new BaremeFrais();
        $data = [
            'title'       => 'Barèmes de frais',
            'title_brand' => 'Espace Opérateur',
            'sidebar'     => true,
            'nav'         => $this->nav('admin/baremes'),
            'baremes'     => $bareme->select('bareme_frais.*, type_operation.nom as type')
                                    ->join('type_operation', 'type_operation.id = bareme_frais.type_operation_id', 'left')
                                    ->orderBy('type_operation.nom')
                                    ->orderBy('montant_min')
                                    ->findAll(),
            'types'       => (new TypeOperation())->orderBy('nom')->findAll(),
        ];
        return view('admin/baremes', $data);
    }

    public function baremeCreate()
    {
        $bareme = new BaremeFrais();
        $rules = [
            'type_operation_id' => 'required|integer',
            'montant_min'       => 'required|numeric',
            'montant_max'       => 'required|numeric',
            'frais'             => 'required|numeric',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Veuillez remplir tous les champs correctement.');
        }
        $bareme->insert($this->request->getPost());
        return redirect()->to('admin/baremes')->with('success', 'Tranche de frais ajoutée.');
    }

    public function baremeDelete($id)
    {
        (new BaremeFrais())->delete($id);
        return redirect()->to('admin/baremes')->with('success', 'Tranche de frais supprimée.');
    }

    public function baremeEdit($id)
    {
        $bareme = (new BaremeFrais())->find($id);
        if (! $bareme) {
            return redirect()->to('admin/baremes')->with('error', 'Barème introuvable.');
        }
        $data = [
            'title'       => 'Modifier un barème',
            'title_brand' => 'Espace Opérateur',
            'sidebar'     => true,
            'nav'         => $this->nav('admin/baremes'),
            'bareme'      => $bareme,
            'types'       => (new TypeOperation())->orderBy('nom')->findAll(),
        ];
        return view('admin/bareme_edit', $data);
    }

    public function baremeUpdate($id)
    {
        $bareme = (new BaremeFrais())->find($id);
        if (! $bareme) {
            return redirect()->to('admin/baremes')->with('error', 'Barème introuvable.');
        }
        $rules = [
            'type_operation_id' => 'required|integer',
            'montant_min'       => 'required|numeric',
            'montant_max'       => 'required|numeric',
            'frais'             => 'required|numeric',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez remplir tous les champs correctement.');
        }
        (new BaremeFrais())->update($id, $this->request->getPost());
        return redirect()->to('admin/baremes')->with('success', 'Barème mis à jour.');
    }

    // ---------- COMMISSIONS INTER-OPERATEUR ----------
    public function commissions()
    {
        $com = new \App\Models\CommissionInterOperateur();
        $data = [
            'title'       => 'Commissions inter-opérateur',
            'title_brand' => 'Espace Opérateur',
            'sidebar'     => true,
            'nav'         => $this->nav('admin/commissions'),
            'commissions' => $com->select('commission_inter_operateur.*, os.nom as src, od.nom as dst')
                                    ->join('operateur os', 'os.id = commission_inter_operateur.operateur_source_id', 'left')
                                    ->join('operateur od', 'od.id = commission_inter_operateur.operateur_destination_id', 'left')
                                    ->orderBy('os.nom')->orderBy('od.nom')->orderBy('montant_min')
                                    ->findAll(),
            'operateurs'  => (new OperateurModel())->orderBy('nom')->findAll(),
        ];
        return view('admin/commissions', $data);
    }

    public function commissionCreate()
    {
        $com = new \App\Models\CommissionInterOperateur();
        $rules = [
            'operateur_source_id'      => 'required|integer',
            'operateur_destination_id' => 'required|integer',
            'montant_min'              => 'required|numeric',
            'montant_max'              => 'required|numeric',
            'pourcentage'              => 'required|numeric',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Veuillez remplir tous les champs correctement.');
        }
        $post = $this->request->getPost();
        if ($post['operateur_source_id'] == $post['operateur_destination_id']) {
            return redirect()->back()->with('error', 'L\'opérateur source et destination doivent être différents.');
        }
        $com->insert($post);
        return redirect()->to('admin/commissions')->with('success', 'Tranche de commission ajoutée.');
    }

    public function commissionDelete($id)
    {
        (new \App\Models\CommissionInterOperateur())->delete($id);
        return redirect()->to('admin/commissions')->with('success', 'Tranche de commission supprimée.');
    }

    // ---------- GAINS ----------
    public function gains()
    {
        $op = new Operation();
        $rows = $op->select('type_operation.nom as type, SUM(operation.frais) as total_frais, COUNT(*) as nb')
                   ->join('type_operation', 'type_operation.id = operation.type_operation_id')
                   ->groupBy('type_operation.nom')
                   ->findAll();

        $totalFrais = 0;
        $retrait = 0;
        $transfertFrais = 0;
        foreach ($rows as $r) {
            $totalFrais += $r['total_frais'];
            if (strtolower($r['type']) === 'retrait') {
                $retrait += $r['total_frais'];
            }
            if (strtolower($r['type']) === 'transfert') {
                $transfertFrais += $r['total_frais'];
            }
        }

        $totalCommission = (new Operation())
            ->join('type_operation', 'type_operation.id = operation.type_operation_id')
            ->where('type_operation.nom', 'transfert')
            ->selectSum('operation.commission')
            ->first()['commission'] ?? 0;

        $data = [
            'title'       => 'Situation des gains',
            'title_brand' => 'Espace Opérateur',
            'sidebar'     => true,
            'nav'         => $this->nav('admin/gains'),
            'rows'        => $rows,
            'total_frais' => $totalFrais,
            'retrait'     => $retrait,
            'transfert_frais' => $transfertFrais,
            'total_commission' => $totalCommission,
            'net'         => $totalFrais - $totalCommission,
        ];
        return view('admin/gains', $data);
    }

    // ---------- SITUATION DES COMPTES ----------
    public function comptes()
    {
        $compte = new Compte();
        $data = [
            'title'       => 'Situation des comptes clients',
            'title_brand' => 'Espace Opérateur',
            'sidebar'     => true,
            'nav'         => $this->nav('admin/comptes'),
            'comptes'     => $compte->select('compte.*, client.telephone, client.nom')
                                    ->join('client', 'client.id = compte.client_id')
                                    ->orderBy('compte.solde', 'desc')
                                    ->findAll(),
            'totalSolde'  => $compte->selectSum('solde')->first()['solde'] ?? 0,
            'nbClients'   => (new \App\Models\Client())->countAll(),
        ];
        return view('admin/comptes', $data);
    }
}
