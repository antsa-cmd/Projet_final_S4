<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Opérateurs</h1>
    <p>Gestion des opérateurs téléphoniques et de leurs préfixes.</p>
</div>

<div class="card">
    <table class="table">
        <thead><tr><th>Nom</th><th>Préfixes</th><th class="right">Actions</th></tr></thead>
        <tbody>
        <?php if (empty($operateurs)): ?>
            <tr><td colspan="3" class="muted">Aucun opérateur.</td></tr>
        <?php endif; ?>
        <?php foreach ($operateurs as $o): ?>
            <tr>
                <td><strong><?= esc($o['nom']) ?></strong></td>
                <td><span class="pill"><?= $o['nb_prefixes'] ?> préfixe(s)</span></td>
                <td class="right"><a class="btn danger sm" href="<?= site_url('admin/operateur/delete/' . $o['id']) ?>" onclick="return confirm('Supprimer cet opérateur ?');"><i data-lucide="trash-2"></i></a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<button class="fab" onclick="openModal()" title="Ajouter un opérateur"><i data-lucide="plus"></i></button>

<div class="modal-bg" id="modal">
    <div class="modal">
        <button class="close" onclick="closeModal()">&times;</button>
        <h3>Ajouter un opérateur</h3>
        <form method="post" action="<?= site_url('admin/operateur') ?>">
            <div class="field">
                <label>Nom de l'opérateur</label>
                <input type="text" name="nom" placeholder="ex : Telma" required>
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
