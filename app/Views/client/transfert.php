<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Transfert</h1>
    <p>Envoyez de l'argent à un ou plusieurs destinataires.</p>
</div>

<div class="narrow">
    <div class="card">
        <form method="post" action="<?= site_url('client/transfert') ?>" id="form">
            <div class="field">
                <label>Numéros des destinataires</label>
                <textarea name="destinataires" id="destinataires" rows="4" placeholder="Un numéro par ligne, ex :&#10;034 12 345 67&#10;032 98 765 43" required></textarea>
                <div class="help">Séparez chaque numéro par un retour à la ligne.</div>
            </div>
            <div id="dest-list" class="alert info" style="display:none;"></div>

            <div class="field">
                <label>Montant total à transférer (Ar)</label>
                <input type="number" step="0.01" min="1" name="montant" id="montant" placeholder="0" required>
                <div class="help">Ce montant sera divisé équitablement entre tous les destinataires.</div>
            </div>

            <div class="field" style="display:flex;align-items:center;gap:10px;margin-top:4px;">
                <input type="checkbox" name="inclure_frais_retrait" id="inclure_frais_retrait" value="1" style="width:18px;height:18px;accent-color:var(--ink);">
                <label for="inclure_frais_retrait" style="margin:0;font-weight:500;color:var(--ink);">Inclure les frais de retrait dans le montant envoyé</label>
            </div>
            <div class="help" style="margin-top:-8px;margin-bottom:14px;">Le destinataire recevra le montant net sans déduction supplémentaire.</div>

            <div class="summary" id="summary" style="display:none;">
                <div class="row"><span class="k">Montant total</span><span class="v tnum" id="s-montant">0 Ar</span></div>
                <div class="row"><span class="k">Frais de transfert</span><span class="v tnum" id="s-frais">0 Ar</span></div>
                <div class="row" id="row-commission" style="display:none;"><span class="k">Commission inter-opérateur</span><span class="v tnum" id="s-commission">0 Ar</span></div>
                <div class="row" id="row-frais-retrait" style="display:none;"><span class="k">Frais de retrait inclus</span><span class="v tnum" id="s-frais-retrait">0 Ar</span></div>
                <div class="row total"><span>Total à débiter</span><span class="tnum" id="s-total">0 Ar</span></div>
                <div class="row" id="row-par-dest" style="display:none;"><span class="k">Par destinataire</span><span class="v tnum" id="s-par-dest">0 Ar</span></div>
            </div>

            <button class="btn block" type="submit"><i data-lucide="send"></i> Envoyer</button>
        </form>
        <a class="btn ghost block mt16" href="<?= site_url('client/dashboard') ?>">Annuler</a>
    </div>
</div>

<?php $this->section('scripts'); ?>
<script>
var srcTel = <?= json_encode($telephone) ?>;
var destTimer;
var firstDest = '';

function parseDestines() {
    var raw = document.getElementById('destinataires').value.trim();
    return raw.split('\n').map(function (l) { return l.trim(); }).filter(function (l) { return l.length >= 9; });
}

function updateDestList() {
    var list = parseDestines();
    var box = document.getElementById('dest-list');
    if (list.length === 0) { box.style.display = 'none'; return; }
    firstDest = list[0];
    box.style.display = 'block';
    box.textContent = list.length + ' destinataire(s) détecté(s) : ' + list.join(', ');
}

function updateSummary(m, nbDest) {
    var inclure = document.getElementById('inclure_frais_retrait').checked ? 1 : 0;
    var url = '<?= site_url('client/frais') ?>?type=transfert&montant=' + m + '&inclure_frais_retrait=' + inclure + '&nb_dest=' + nbDest;
    if (firstDest.length >= 9) {
        url += '&dest=' + encodeURIComponent(firstDest);
    }
    fetch(url)
        .then(r => r.json()).then(function (d) {
            var box = document.getElementById('summary');
            box.style.display = 'block';
            document.getElementById('s-montant').textContent = Number(m).toLocaleString('fr-FR') + ' Ar';
            document.getElementById('s-frais').textContent = Number(d.frais).toLocaleString('fr-FR') + ' Ar';
            var rowComm = document.getElementById('row-commission');
            if (d.commission > 0) {
                rowComm.style.display = 'flex';
                document.getElementById('s-commission').textContent = Number(d.commission).toLocaleString('fr-FR') + ' Ar';
            } else {
                rowComm.style.display = 'none';
            }
            var rowFraisRetrait = document.getElementById('row-frais-retrait');
            if (d.fraisRetraitSupplementaire > 0) {
                rowFraisRetrait.style.display = 'flex';
                document.getElementById('s-frais-retrait').textContent = Number(d.fraisRetraitSupplementaire).toLocaleString('fr-FR') + ' Ar';
            } else {
                rowFraisRetrait.style.display = 'none';
            }
            document.getElementById('s-total').textContent = Number(d.total).toLocaleString('fr-FR') + ' Ar';
            var rowParDest = document.getElementById('row-par-dest');
            if (nbDest > 1) {
                rowParDest.style.display = 'flex';
                document.getElementById('s-par-dest').textContent = (Number(d.total) / nbDest).toLocaleString('fr-FR') + ' Ar par destinataire';
            } else {
                rowParDest.style.display = 'none';
            }
        });
}

document.getElementById('destinataires').addEventListener('input', function () {
    clearTimeout(destTimer);
    updateDestList();
    var m = parseFloat(document.getElementById('montant').value) || 0;
    var list = parseDestines();
    if (m > 0 && list.length > 0) {
        updateSummary(m, list.length);
    }
});

document.getElementById('montant').addEventListener('input', function () {
    var m = parseFloat(this.value) || 0;
    var list = parseDestines();
    if (m <= 0) { document.getElementById('summary').style.display = 'none'; return; }
    updateSummary(m, list.length > 0 ? list.length : 1);
});

document.getElementById('inclure_frais_retrait').addEventListener('change', function () {
    var m = parseFloat(document.getElementById('montant').value) || 0;
    var list = parseDestines();
    if (m > 0 && list.length > 0) {
        updateSummary(m, list.length);
    }
});
</script>
<?php $this->endSection(); ?>
<?php $this->endSection(); ?>
