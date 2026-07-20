<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Mon solde</h1>
    <p>Compte <?= esc($telephone) ?></p>
</div>

<div class="balance">
    <div class="label">Solde disponible</div>
    <div class="amount tnum"><?= number_format($solde, 2, ',', ' ') ?> Ar</div>
</div>

<a class="btn ghost block mt24" href="<?= site_url('client/dashboard') ?>"><i data-lucide="arrow-left"></i> Retour</a>
<?php $this->endSection(); ?>
