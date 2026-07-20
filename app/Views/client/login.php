<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="auth">
    <div class="auth-card">
        <div class="auth-illus"><i data-lucide="smartphone"></i></div>
        <h1>Mobile Money</h1>
        <p class="lead">Connectez-vous avec votre numéro. Création automatique du compte.</p>

        <form method="post" action="<?= site_url('client/login') ?>">
            <div class="field">
                <label>Numéro de téléphone</label>
                <input type="tel" name="telephone" placeholder="ex : 034 12 345 67" value="<?= old('telephone') ?>" required autofocus>
            </div>
            <button class="btn block" type="submit">
                <i data-lucide="arrow-right"></i> Continuer
            </button>
        </form>
    </div>
</div>
<?php $this->endSection(); ?>
