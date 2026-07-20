<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Modifier un barème</h1>
    <p>Modifiez la tranche de frais sélectionnée.</p>
</div>

<div class="narrow">
    <div class="card">
        <form method="post" action="<?= site_url('admin/bareme/edit/' . $bareme['id']) ?>">
            <div class="field">
                <label>Type d'opération</label>
                <select name="type_operation_id" required>
                    <option value="">-- choisir --</option>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= $t['id'] == $bareme['type_operation_id'] ? 'selected' : '' ?>>
                            <?= esc($t['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex;gap:12px;">
                <div class="field" style="flex:1;">
                    <label>Montant min</label>
                    <input type="number" step="0.01" name="montant_min" value="<?= esc($bareme['montant_min']) ?>" required>
                </div>
                <div class="field" style="flex:1;">
                    <label>Montant max</label>
                    <input type="number" step="0.01" name="montant_max" value="<?= esc($bareme['montant_max']) ?>" required>
                </div>
            </div>
            <div class="field">
                <label>Frais (Ar)</label>
                <input type="number" step="0.01" name="frais" value="<?= esc($bareme['frais']) ?>" required>
            </div>
            <div style="display:flex;gap:12px;">
                <button class="btn block" type="submit"><i data-lucide="check"></i> Enregistrer</button>
                <a class="btn ghost block" href="<?= site_url('admin/baremes') ?>">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php $this->section('scripts'); ?>
<script>
if (window.lucide) lucide.createIcons();
</script>
<?php $this->endSection(); ?>
<?php $this->endSection(); ?>
