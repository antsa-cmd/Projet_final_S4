<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Retrait</h1>
    <p>Retirez de l'argent de votre compte.</p>
</div>

<div class="narrow">
    <div class="card">
        <form method="post" action="<?= site_url('client/retrait') ?>" id="form">
            <div class="field">
                <label>Montant à retirer (Ar)</label>
                <input type="number" step="0.01" min="1" name="montant" id="montant" placeholder="0" required>
            </div>

            <div class="summary" id="summary" style="display:none;">
                <div class="row"><span class="k">Montant</span><span class="v tnum" id="s-montant">0 Ar</span></div>
                <div class="row"><span class="k">Frais</span><span class="v tnum" id="s-frais">0 Ar</span></div>
                <div class="row"><span class="k">Total débité</span><span class="v tnum" id="s-total">0 Ar</span></div>
                <div class="row total"><span>Solde restant</span><span class="tnum" id="s-reste">0 Ar</span></div>
            </div>

            <button class="btn block" type="submit"><i data-lucide="check"></i> Confirmer le retrait</button>
        </form>
        <a class="btn ghost block mt16" href="<?= site_url('client/dashboard') ?>">Annuler</a>
    </div>
</div>

<?php $this->section('scripts'); ?>
<script>
var solde = <?= $solde ?>;
document.getElementById('montant').addEventListener('input', function () {
    var m = parseFloat(this.value) || 0;
    var box = document.getElementById('summary');
    if (m <= 0) { box.style.display = 'none'; return; }
    fetch('<?= site_url('client/frais') ?>?type=retrait&montant=' + m)
        .then(r => r.json()).then(function (d) {
            box.style.display = 'block';
            document.getElementById('s-montant').textContent = m.toLocaleString('fr-FR') + ' Ar';
            document.getElementById('s-frais').textContent = Number(d.frais).toLocaleString('fr-FR') + ' Ar';
            document.getElementById('s-total').textContent = Number(d.total).toLocaleString('fr-FR') + ' Ar';
            document.getElementById('s-reste').textContent = (solde - d.total).toLocaleString('fr-FR') + ' Ar';
        });
});
</script>
<?php $this->endSection(); ?>
<?php $this->endSection(); ?>
