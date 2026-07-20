<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Comptes clients</h1>
    <p>Situation des soldes de tous les clients.</p>
</div>

<div class="grid grid-2" style="max-width:520px;">
    <div class="stat">
        <div class="ic"><i data-lucide="users"></i></div>
        <div class="n tnum"><?= $nbClients ?></div>
        <div class="l">Clients</div>
    </div>
    <div class="stat">
        <div class="ic"><i data-lucide="wallet"></i></div>
        <div class="n tnum"><?= number_format($totalSolde, 0, ',', ' ') ?> Ar</div>
        <div class="l">Solde total</div>
    </div>
</div>

<div class="card mt24">
    <h2>Liste des comptes</h2>
    <div class="sub">Triés par solde décroissant</div>
    <table class="table">
        <thead><tr><th>Téléphone</th><th>Nom</th><th class="right">Solde (Ar)</th></tr></thead>
        <tbody>
        <?php if (empty($comptes)): ?>
            <tr><td colspan="3" class="muted">Aucun compte.</td></tr>
        <?php endif; ?>
        <?php foreach ($comptes as $c): ?>
            <tr>
                <td class="tnum"><?= esc($c['telephone']) ?></td>
                <td><?= esc($c['nom'] ?? '—') ?></td>
                <td class="right tnum"><strong><?= number_format($c['solde'], 0, ',', ' ') ?></strong></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php $this->endSection(); ?>
