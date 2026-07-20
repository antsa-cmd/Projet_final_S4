<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'MVola') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
<?php if (isset($sidebar) && $sidebar): ?>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <span class="logo"><i data-lucide="wallet"></i></span>
            <?= esc($title_brand ?? 'MVola') ?>
        </div>
        <?php foreach (($nav ?? []) as $item): ?>
            <a class="navlink <?= ($item['active'] ?? false) ? 'active' : '' ?>" href="<?= site_url($item['url']) ?>">
                <i data-lucide="<?= $item['icon'] ?>"></i> <?= esc($item['label']) ?>
            </a>
        <?php endforeach; ?>
        <div class="spacer"></div>
        <a class="navlink" href="<?= site_url('client') ?>"><i data-lucide="smartphone"></i> Espace client</a>
    </aside>
    <main class="main">
        <?= $this->renderSection('content') ?>
    </main>
</div>
<?php else: ?>
    <header class="topbar">
        <div class="brand">
            <span class="logo"><i data-lucide="wallet"></i></span>
            <?= esc($title_brand ?? 'MVola') ?>
        </div>
        <nav>
            <?php foreach (($nav ?? []) as $label => $url): ?>
                <a href="<?= site_url($url) ?>"><?= esc($label) ?></a>
            <?php endforeach; ?>
        </nav>
    </header>
    <div class="wrap">
        <?= $this->renderSection('content') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert success" style="position:fixed;top:20px;right:20px;z-index:99;margin:0;box-shadow:var(--shadow-lg);"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert danger" style="position:fixed;top:20px;right:20px;z-index:99;margin:0;box-shadow:var(--shadow-lg);"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<script>
    if (window.lucide) lucide.createIcons();
    document.querySelectorAll('.alert').forEach(function (el) {
        setTimeout(function () { el.style.transition = 'opacity .4s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 400); }, 3500);
    });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
