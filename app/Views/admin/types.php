<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<div class="page-head">
    <h1>Types d'opération</h1>
    <p>Création des types d'opérations (dépôt, retrait, transfert).</p>
</div>

<div class="card">
    <form method="post" action="<?= site_url('admin/types') ?>" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:220px;">
            <label class="muted" style="font-size:13.5px;">Nom du type</label>
            <input type="text" name="nom" placeholder="ex : dépôt" required>
        </div>
        <button class="btn" type="submit"><i data-lucide="plus"></i> Ajouter</button>
    </form>
</div>

<div class="card mt24">
    <h2>Types définis</h2>
    <div class="sub">Liste des types d'opération</div>
    <table class="table">
        <thead><tr><th>Nom</th><th class="right">Actions</th></tr></thead>
        <tbody>
        <?php if (empty($types)): ?>
            <tr><td colspan="2" class="muted">Aucun type défini.</td></tr>
        <?php endif; ?>
        <?php foreach ($types as $t): ?>
            <tr>
                <td><strong><?= esc($t['nom']) ?></strong></td>
                <td class="right"><a class="btn danger sm" href="<?= site_url('admin/type/delete/' . $t['id']) ?>" onclick="return confirm('Supprimer ?');"><i data-lucide="trash-2"></i></a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php $this->endSection(); ?>
