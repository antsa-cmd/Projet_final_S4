<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Historique</h1>
    <p>Vue d'ensemble de la plateforme Mobile Money.</p>
</div>

<div class="grid grid-4">
    <div class="stat">
        <div class="ic"><i data-lucide="users"></i></div>
        <div class="n tnum"><?= $nbClients ?></div>
        <div class="l">Clients</div>
    </div>
    <div class="stat">
        <div class="ic"><i data-lucide="repeat"></i></div>
        <div class="n tnum"><?= $nbOps ?></div>
        <div class="l">Opérations</div>
    </div>
    <div class="stat">
        <div class="ic"><i data-lucide="trending-up"></i></div>
        <div class="n tnum"><?= number_format($gains, 0, ',', ' ') ?> Ar</div>
        <div class="l">Gains des frais</div>
    </div>
    <div class="stat">
        <div class="ic"><i data-lucide="wallet"></i></div>
        <div class="n tnum"><?= number_format($totalSolde, 0, ',', ' ') ?> Ar</div>
        <div class="l">Solde total</div>
    </div>
</div>

<div class="card mt24">
    <h2>Transactions récentes</h2>
    <div class="sub">Les dernières opérations enregistrées</div>
    <table class="table">
        <thead><tr><th>Date</th><th>Type</th><th>Client</th><th>Montant</th><th>Frais</th></tr></thead>
        <tbody>
        <?php if (empty($recent)): ?>
            <tr><td colspan="5" class="muted">Aucune opération.</td></tr>
        <?php endif; ?>
        <?php foreach ($recent as $r): ?>
            <tr>
                <td class="muted tnum"><?= esc($r['date_operation']) ?></td>
                <td><span class="pill accent"><?= esc($r['type']) ?></span></td>
                <td class="tnum"><?= esc($r['telephone'] ?? '—') ?></td>
                <td class="tnum"><?= number_format($r['montant'], 0, ',', ' ') ?> Ar</td>
                <td class="muted tnum"><?= number_format($r['frais'], 0, ',', ' ') ?> Ar</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php $this->endSection(); ?>
