<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Gains</h1>
    <p>Situation des gains générés par les frais et commissions inter-opérateurs.</p>
</div>

<div class="grid grid-3">
    <div class="stat">
        <div class="ic"><i data-lucide="pie-chart"></i></div>
        <div class="n tnum"><?= number_format($total_frais, 0, ',', ' ') ?> Ar</div>
        <div class="l">Gain total opérateur (frais)</div>
    </div>
    <div class="stat">
        <div class="ic"><i data-lucide="arrow-left-right"></i></div>
        <div class="n tnum"><?= number_format($total_commission, 0, ',', ' ') ?> Ar</div>
        <div class="l">Commissions autres opérateurs</div>
    </div>
    <div class="stat">
        <div class="ic"><i data-lucide="wallet"></i></div>
        <div class="n tnum"><?= number_format($net, 0, ',', ' ') ?> Ar</div>
        <div class="l">Gain net</div>
    </div>
</div>

<div class="card mt24">
    <h2>Détail par type d'opération</h2>
    <div class="sub">Total des frais collectés par l'opérateur</div>
    <div class="chart-box" style="height:220px;"><canvas id="gainsChart"></canvas></div>
    <table class="table mt24">
        <thead><tr><th>Type</th><th>Nombre</th><th>Frais (Ar)</th></tr></thead>
        <tbody>
        <?php if (empty($rows)): ?>
            <tr><td colspan="3" class="muted">Aucune opération enregistrée.</td></tr>
        <?php endif; ?>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><span class="pill accent"><?= esc($r['type']) ?></span></td>
                <td class="tnum"><?= $r['nb'] ?></td>
                <td class="tnum"><?= number_format($r['total_frais'], 0, ',', ' ') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="card mt24">
    <h2>Détail transferts : frais vs commission</h2>
    <div class="sub">Répartition entre gain opérateur et commission versée aux autres opérateurs</div>
    <table class="table mt24">
        <thead><tr><th>Type</th><th>Nombre</th><th>Frais opérateur (Ar)</th><th>Commission autres opérateurs (Ar)</th></tr></thead>
        <tbody>
        <?php
        $transfertRow = null;
        foreach ($rows as $r) {
            if (strtolower($r['type']) === 'transfert') {
                $transfertRow = $r;
                break;
            }
        }
        if (! $transfertRow): ?>
            <tr><td colspan="4" class="muted">Aucun transfert enregistré.</td></tr>
        <?php endif; ?>
        <?php if ($transfertRow): ?>
            <tr>
                <td><span class="pill accent"><?= esc($transfertRow['type']) ?></span></td>
                <td class="tnum"><?= $transfertRow['nb'] ?></td>
                <td class="tnum"><?= number_format($transfertRow['total_frais'], 0, ',', ' ') ?></td>
                <td class="tnum"><?= number_format($total_commission, 0, ',', ' ') ?></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $this->section('scripts'); ?>
<script>
var rows = <?= json_encode(array_map(fn($r) => ['type' => $r['type'], 'gain' => (float)$r['total_frais']], $rows)) ?>;
new Chart(document.getElementById('gainsChart'), {
    type: 'bar',
    data: { labels: rows.map(r => r.type), datasets: [{ data: rows.map(r => r.gain), backgroundColor: '#D3FF01', borderRadius: 8, borderSkipped: false }] },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f0f0f0' } }, x: { grid: { display: false } } } }
});
</script>
<?php $this->endSection(); ?>
<?php $this->endSection(); ?>
