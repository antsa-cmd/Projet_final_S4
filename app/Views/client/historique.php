<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Historique</h1>
    <p>Toutes vos opérations Mobile Money.</p>
</div>

<div class="card">
    <div class="filters">
        <button class="chip active" data-f="all">Tous</button>
        <button class="chip" data-f="dépôt">Dépôt</button>
        <button class="chip" data-f="retrait">Retrait</button>
        <button class="chip" data-f="transfert">Transfert</button>
    </div>

    <?php if (empty($operations)): ?>
        <div class="empty">Aucune opération enregistrée.</div>
    <?php else: ?>
        <table class="table" id="tx-table">
            <thead>
                <tr><th>Date</th><th>Opération</th><th>Montant</th><th>Frais</th><th>Frais retrait</th><th>Solde</th></tr>
            </thead>
            <tbody>
            <?php foreach ($operations as $o): ?>
                <?php
                    $isDepot = ($o['compte_source'] == $monCompte && $o['compte_destination'] == $monCompte);
                    $isOut   = ($o['compte_source'] == $monCompte && ! $isDepot);
                    $typeKey = $isDepot ? 'dépôt' : $o['type'];
                    $icon = $isDepot ? 'arrow-down-left' : ($o['type'] === 'transfert' ? 'send' : 'arrow-up-right');
                    $sign = $isOut ? '-' : '+';
                    $cls  = $isOut ? 'neg' : 'pos';
                ?>
                <tr data-type="<?= $typeKey ?>">
                    <td class="muted tnum"><?= esc($o['date_operation']) ?></td>
                    <td>
                        <div class="tx" style="padding:0;border:none;">
                            <div class="ic <?= $isOut ? 'out' : 'in' ?>" style="width:34px;height:34px;border-radius:10px;">
                                <i data-lucide="<?= $icon ?>" style="width:17px;height:17px;"></i>
                            </div>
                            <div class="body"><div class="t" style="font-size:14px;"><?= ucfirst(esc($o['type'])) ?></div></div>
                        </div>
                    </td>
                    <td class="tnum"><strong class="amt <?= $cls ?>" style="font-weight:600;"><?= $sign ?><?= number_format($o['montant'], 0, ',', ' ') ?> Ar</strong></td>
                    <td class="muted tnum"><?= number_format($o['frais'], 0, ',', ' ') ?> Ar</td>
                    <td class="muted tnum"><?= $o['frais_retrait'] > 0 ? number_format($o['frais_retrait'], 0, ',', ' ') . ' Ar' : '—' ?></td>
                    <td class="tnum">—</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a class="btn ghost mt24" href="<?= site_url('client/dashboard') ?>"><i data-lucide="arrow-left"></i> Retour</a>
</div>

<?php $this->section('scripts'); ?>
<script>
document.querySelectorAll('.chip').forEach(function (c) {
    c.addEventListener('click', function () {
        document.querySelectorAll('.chip').forEach(x => x.classList.remove('active'));
        c.classList.add('active');
        var f = c.dataset.f;
        document.querySelectorAll('#tx-table tbody tr').forEach(function (tr) {
            tr.style.display = (f === 'all' || tr.dataset.type === f) ? '' : 'none';
        });
    });
});
</script>
<?php $this->endSection(); ?>
<?php $this->endSection(); ?>
