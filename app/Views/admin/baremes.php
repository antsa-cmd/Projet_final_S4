<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Barèmes de frais</h1>
    <p>Frais par tranche de montant, modifiables à tout moment.</p>
</div>

<div class="card">
    <table class="table">
        <thead><tr><th>Type</th><th>Min (Ar)</th><th>Max (Ar)</th><th>Frais (Ar)</th><th class="right">Actions</th></tr></thead>
        <tbody>
        <?php if (empty($baremes)): ?>
            <tr><td colspan="5" class="muted">Aucun barème défini.</td></tr>
        <?php endif; ?>
        <?php foreach ($baremes as $b): ?>
            <tr>
                <td><span class="pill accent"><?= esc($b['type']) ?></span></td>
                <td class="tnum"><?= number_format($b['montant_min'], 0, ',', ' ') ?></td>
                <td class="tnum"><?= number_format($b['montant_max'], 0, ',', ' ') ?></td>
                <td class="tnum"><?= number_format($b['frais'], 0, ',', ' ') ?></td>
                <td class="right"><a class="btn danger sm" href="<?= site_url('admin/bareme/delete/' . $b['id']) ?>" onclick="return confirm('Supprimer ?');"><i data-lucide="trash-2"></i></a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<button class="fab" onclick="openModal()" title="Ajouter un barème"><i data-lucide="plus"></i></button>

<div class="modal-bg" id="modal">
    <div class="modal">
        <button class="close" onclick="closeModal()">&times;</button>
        <h3>Ajouter une tranche</h3>
        <form method="post" action="<?= site_url('admin/baremes') ?>">
            <div class="field">
                <label>Type d'opération</label>
                <select name="type_operation_id" required>
                    <option value="">-- choisir --</option>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= esc($t['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex;gap:12px;">
                <div class="field" style="flex:1;">
                    <label>Montant min</label>
                    <input type="number" step="0.01" name="montant_min" required>
                </div>
                <div class="field" style="flex:1;">
                    <label>Montant max</label>
                    <input type="number" step="0.01" name="montant_max" required>
                </div>
            </div>
            <div class="field">
                <label>Frais (Ar)</label>
                <input type="number" step="0.01" name="frais" required>
            </div>
            <button class="btn block" type="submit"><i data-lucide="check"></i> Ajouter</button>
        </form>
    </div>
</div>

<?php $this->section('scripts'); ?>
<script>
function openModal() { document.getElementById('modal').classList.add('open'); }
function closeModal() { document.getElementById('modal').classList.remove('open'); }
document.getElementById('modal').addEventListener('click', function (e) { if (e.target === this) closeModal(); });
</script>
<?php $this->endSection(); ?>
<?php $this->endSection(); ?>
