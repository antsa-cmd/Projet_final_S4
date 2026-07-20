<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Préfixes</h1>
    <p>Configuration des préfixes par opérateur (ex : 033, 037).</p>
</div>

<div class="card">
    <h2>Ajouter un opérateur</h2>
    <div class="sub">Si l'opérateur n'existe pas encore</div>
    <form method="post" action="<?= site_url('admin/operateur') ?>" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:200px;">
            <label class="muted" style="font-size:13.5px;">Nom</label>
            <input type="text" name="nom" placeholder="ex : Telma" required>
        </div>
        <button class="btn dark" type="submit"><i data-lucide="plus"></i> Opérateur</button>
    </form>
</div>

<div class="card mt24">
    <h2>Préfixes existants</h2>
    <div class="sub">Liste des préfixes configurés</div>
    <table class="table">
        <thead><tr><th>Préfixe</th><th>Opérateur</th><th class="right">Actions</th></tr></thead>
        <tbody>
        <?php if (empty($prefixes)): ?>
            <tr><td colspan="3" class="muted">Aucun préfixe configuré.</td></tr>
        <?php endif; ?>
        <?php foreach ($prefixes as $p): ?>
            <tr>
                <td><span class="pill accent"><?= esc($p['prefixe']) ?></span></td>
                <td><?= esc($p['operateur']) ?></td>
                <td class="right"><a class="btn danger sm" href="<?= site_url('admin/prefixe/delete/' . $p['id']) ?>" onclick="return confirm('Supprimer ce préfixe ?');"><i data-lucide="trash-2"></i></a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<button class="fab" onclick="openModal()" title="Ajouter un préfixe"><i data-lucide="plus"></i></button>

<div class="modal-bg" id="modal">
    <div class="modal">
        <button class="close" onclick="closeModal()">&times;</button>
        <h3>Ajouter un préfixe</h3>
        <form method="post" action="<?= site_url('admin/prefixes') ?>">
            <div class="field">
                <label>Préfixe</label>
                <input type="text" name="prefixe" placeholder="ex : 033" required>
            </div>
            <div class="field">
                <label>Opérateur</label>
                <select name="operateur_id" required>
                    <option value="">-- choisir --</option>
                    <?php foreach ($operateurs as $o): ?>
                        <option value="<?= $o['id'] ?>"><?= esc($o['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
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
