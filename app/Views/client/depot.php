<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Dépôt</h1>
    <p>Approvisionnez votre compte.</p>
</div>

<div class="narrow">
    <div class="card">
        <form method="post" action="<?= site_url('client/depot') ?>" id="form">
            <div class="field">
                <label>Montant à déposer (Ar)</label>
                <input type="number" step="0.01" min="1" name="montant" id="montant" placeholder="0" required>
            </div>

            <div class="summary" id="summary" style="display:none;">
                <div class="row"><span class="k">Montant</span><span class="v tnum" id="s-montant">0 Ar</span></div>
                <div class="row"><span class="k">Frais</span><span class="v tnum" id="s-frais">0 Ar</span></div>
                <div class="row total"><span>Nouveau solde</span><span class="tnum" id="s-total">0 Ar</span></div>
            </div>

            <button class="btn block" type="submit"><i data-lucide="check"></i> Valider le dépôt</button>
        </form>
        <a class="btn ghost block mt16" href="<?= site_url('client/dashboard') ?>">Annuler</a>
    </div>
</div>

<?php $this->section('scripts'); ?>
<script>
document.getElementById('montant').addEventListener('input', function () {
    var m = parseFloat(this.value) || 0;
    var box = document.getElementById('summary');
    if (m > 0) {
        box.style.display = 'block';
        document.getElementById('s-montant').textContent = m.toLocaleString('fr-FR') + ' Ar';
        document.getElementById('s-frais').textContent = '0 Ar (offert)';
        document.getElementById('s-total').textContent = m.toLocaleString('fr-FR') + ' Ar';
    } else {
        box.style.display = 'none';
    }
});
</script>
<?php $this->endSection(); ?>
<?php $this->endSection(); ?>
