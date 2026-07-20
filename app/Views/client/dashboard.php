<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Bonjour </h1>
    <p>Voici la synthèse de votre compte Mobile Money.</p>
</div>

<div class="balance">
    <div class="label">Solde disponible</div>
    <div class="amount tnum"><?= number_format($solde, 2, ',', ' ') ?> Ar</div>
    <div class="meta">
        <div>
            <div class="k">Numéro</div>
            <div class="v tnum"><?= esc($telephone) ?></div>
        </div>
        <div>
            <div class="k">Opérateur</div>
            <div class="v"><span class="badge-op"><?= esc($operateur) ?></span></div>
        </div>
    </div>
</div>

<div class="actions mt24">
    <a class="action" href="<?= site_url('client/depot') ?>">
        <span class="ic"><i data-lucide="arrow-down-left"></i></span>
        <span class="t">Dépôt</span>
    </a>
    <a class="action" href="<?= site_url('client/retrait') ?>">
        <span class="ic"><i data-lucide="arrow-up-right"></i></span>
        <span class="t">Retrait</span>
    </a>
    <a class="action" href="<?= site_url('client/transfert') ?>">
        <span class="ic"><i data-lucide="send"></i></span>
        <span class="t">Transfert</span>
    </a>
    <a class="action" href="<?= site_url('client/historique') ?>">
        <span class="ic"><i data-lucide="list"></i></span>
        <span class="t">Historique</span>
    </a>
</div>

<div class="card mt24">
    <div class="row-between">
        <div>
            <h2>Historique récent</h2>
            <div class="sub">Vos 5 dernières opérations</div>
        </div>
        <a class="btn ghost sm" href="<?= site_url('client/historique') ?>">Tout voir</a>
    </div>

    <?php if (empty($recent)): ?>
        <div class="empty">Aucune opération pour le moment.</div>
    <?php else: ?>
        <?php foreach ($recent as $o): ?>
            <?php
                $isDepot = ($o['compte_source'] == $monCompte && $o['compte_destination'] == $monCompte);
                $isOut   = ($o['compte_source'] == $monCompte && ! $isDepot);
                $icon = $isDepot ? 'arrow-down-left' : ($o['type'] === 'transfert' ? 'send' : 'arrow-up-right');
                $sign = $isOut ? '-' : '+';
                $cls  = $isOut ? 'neg' : 'pos';
            ?>
            <div class="tx">
                <div class="ic <?= $isOut ? 'out' : 'in' ?>"><i data-lucide="<?= $icon ?>"></i></div>
                <div class="body">
                    <div class="t"><?= ucfirst(esc($o['type'])) ?></div>
                    <div class="s tnum"><?= esc($o['date_operation']) ?></div>
                </div>
                <div class="amt <?= $cls ?> tnum"><?= $sign ?><?= number_format($o['montant'], 0, ',', ' ') ?> Ar</div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php $this->endSection(); ?>
