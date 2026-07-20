<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Commissions inter-opérateur</h1>
    <p>Commission prélevée en plus des frais lors d'un transfert vers un autre opérateur.</p>
</div>

<div class="card">
    <table class="table">
        <thead><tr><th>De</th><th>Vers</th><th>Min (Ar)</th><th>Max (Ar)</th><th>Commission (%)</th><th class="right">Actions</th></tr></thead>
        <tbody>
        <?php if (empty($commissions)): ?>
            <tr><td colspan="6" class="muted">Aucune commission définie.</td></tr>
        <?php endif; ?>
        <?php foreach ($commissions as $c): ?>
            <tr>
                <td><span class="pill accent"><?= esc($c['src']) ?></span></td>
                <td><span class="pill"><?= esc($c['dst']) ?></span></td>
                <td class="tnum"><?= number_format($c['montant_min'], 0, ',', ' ') ?></td>
                <td class="tnum"><?= number_format($c['montant_max'], 0, ',', ' ') ?></td>
                <td class="tnum"><?= number_format($c['pourcentage'], 0, ',', ' ') ?> %</td>
                <td class="right"><a class="btn danger sm" href="<?= site_url('admin/commission/delete/' . $c['id']) ?>" onclick="return confirm('Supprimer ?');"><i data-lucide="trash-2"></i></a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<button class="fab" onclick="openModal()" title="Ajouter une commission"><i data-lucide="plus"></i></button>

<div class="modal-bg" id="modal">
    <div class="modal">
        <button class="close" onclick="closeModal()">&times;</button>
        <h3>Ajouter une tranche de commission</h3>
        <form method="post" action="<?= site_url('admin/commissions') ?>">
            <div style="display:flex;gap:12px;">
                <div class="field" style="flex:1;">
                    <label>Opérateur source</label>
                    <select name="operateur_source_id" required>
                        <option value="">-- choisir --</option>
                        <?php foreach ($operateurs as $o): ?>
                            <option value="<?= $o['id'] ?>"><?= esc($o['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field" style="flex:1;">
                    <label>Opérateur destination</label>
                    <select name="operateur_destination_id" required>
                        <option value="">-- choisir --</option>
                        <?php foreach ($operateurs as $o): ?>
                            <option value="<?= $o['id'] ?>"><?= esc($o['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
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
                <label>Commission (%)</label>
                <input type="number" step="0.01" name="pourcentage" required>
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
