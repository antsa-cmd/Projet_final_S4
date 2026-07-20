<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Transfert</h1>
    <p>Envoyez de l'argent à un autre numéro.</p>
</div>

<div class="narrow">
    <div class="card">
        <form method="post" action="<?= site_url('client/transfert') ?>" id="form">
            <div class="field">
                <label>Numéro du destinataire</label>
                <input type="tel" name="destinataire" id="dest" placeholder="ex : 034 76 543 21" required>
            </div>
            <div id="dest-nom" class="alert info" style="display:none;"></div>
            <div id="dest-op" class="alert muted" style="display:none;"></div>
            <div id="src-op" class="alert accent" style="display:none;"></div>

            <div class="field">
                <label>Montant à transférer (Ar)</label>
                <input type="number" step="0.01" min="1" name="montant" id="montant" placeholder="0" required>
            </div>

            <div class="summary" id="summary" style="display:none;">
                <div class="row"><span class="k">Montant</span><span class="v tnum" id="s-montant">0 Ar</span></div>
                <div class="row"><span class="k">Frais</span><span class="v tnum" id="s-frais">0 Ar</span></div>
                <div class="row" id="row-commission" style="display:none;"><span class="k">Commission inter-opérateur</span><span class="v tnum" id="s-commission">0 Ar</span></div>
                <div class="row total"><span>Total à débiter</span><span class="tnum" id="s-total">0 Ar</span></div>
            </div>

            <button class="btn block" type="submit"><i data-lucide="send"></i> Envoyer</button>
        </form>
        <a class="btn ghost block mt16" href="<?= site_url('client/dashboard') ?>">Annuler</a>
    </div>
</div>

<?php $this->section('scripts'); ?>
<script>
var destTimer;
document.getElementById('dest').addEventListener('input', function () {
    clearTimeout(destTimer);
    var tel = this.value.trim();
    var box = document.getElementById('dest-nom');
    var opBox = document.getElementById('dest-op');
    var srcOpBox = document.getElementById('src-op');
    if (tel.length < 9) {
        box.style.display = 'none';
        opBox.style.display = 'none';
        return;
    }
    destTimer = setTimeout(function () {
        fetch('<?= site_url('client/frais') ?>?type=transfert&montant=0&dest=' + encodeURIComponent(tel))
            .then(r => r.json()).then(function (d) {
                if (d.destNom) {
                    box.style.display = 'block';
                    box.textContent = 'Destinataire : ' + d.destNom;
                } else {
                    box.style.display = 'none';
                }
                if (d.destOperateur && d.destOperateur !== 'Inconnu') {
                    opBox.style.display = 'block';
                    opBox.textContent = 'Opérateur destination : ' + d.destOperateur;
                } else {
                    opBox.style.display = 'none';
                }
                if (d.srcOperateur && d.srcOperateur !== 'Inconnu') {
                    srcOpBox.style.display = 'block';
                    srcOpBox.textContent = 'Opérateur source : ' + d.srcOperateur + ' → ' + (d.destOperateur || 'Inconnu');
                } else {
                    srcOpBox.style.display = 'none';
                }
            });
    }, 400);
});

document.getElementById('montant').addEventListener('input', function () {
    var m = parseFloat(this.value) || 0;
    var box = document.getElementById('summary');
    if (m <= 0) { box.style.display = 'none'; return; }
    var dest = document.getElementById('dest').value.trim();
    var url = '<?= site_url('client/frais') ?>?type=transfert&montant=' + m;
    if (dest.length >= 9) {
        url += '&dest=' + encodeURIComponent(dest);
    }
    fetch(url)
        .then(r => r.json()).then(function (d) {
            box.style.display = 'block';
            document.getElementById('s-montant').textContent = m.toLocaleString('fr-FR') + ' Ar';
            document.getElementById('s-frais').textContent = Number(d.frais).toLocaleString('fr-FR') + ' Ar';
            var rowComm = document.getElementById('row-commission');
            if (d.commission > 0) {
                rowComm.style.display = 'flex';
                document.getElementById('s-commission').textContent = Number(d.commission).toLocaleString('fr-FR') + ' Ar';
            } else {
                rowComm.style.display = 'none';
            }
            document.getElementById('s-total').textContent = Number(d.total).toLocaleString('fr-FR') + ' Ar';
        });
});
</script>
<?php $this->endSection(); ?>
<?php $this->endSection(); ?>
