<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang === 'en' ? 'Immune System · Signaling Pathways · Autoimmune Diseases' : 'Immunsystem · Signalwege · Autoimmunerkrankungen' ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            display: flex; flex-direction: column;
            background: <?= e($theme['bg']) ?>;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: <?= e($theme['text']) ?>;
            transition: background .3s, color .3s;
        }
        a { color: inherit; text-decoration: none; }
        .header {
            background: <?= $theme['headerBg'] ?>;
            border-bottom: 1px solid <?= e($theme['border']) ?>;
            padding: 18px 16px 12px;
            text-align: center;
            position: relative;
            transition: background .3s;
        }
        .header h1 {
            font-size: 19px; font-weight: 800; margin: 0;
            background: <?= $dark ? 'linear-gradient(90deg,#88aadd,#cc88dd,#dd8866)' : 'linear-gradient(90deg,#335588,#884488,#885533)' ?>;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .header p { font-size: 10.5px; color: <?= e($theme['textFaint']) ?>; margin: 5px 0 0; }
        .theme-toggle {
            position: absolute; top: 12px; right: 14px;
            background: <?= $dark ? '#ffffff20' : '#00000012' ?>;
            border: none; border-radius: 20px; padding: 5px 12px;
            cursor: pointer; font-size: 13px; color: <?= e($theme['text']) ?>;
        }
        .lang-toggle {
            position: absolute; top: 12px; left: 14px;
            background: <?= $dark ? '#ffffff20' : '#00000012' ?>;
            border: none; border-radius: 20px; padding: 5px 12px;
            cursor: pointer; font-size: 11px; font-weight: 600;
            color: <?= e($theme['text']) ?>;
        }
        .tabs {
            display: flex; border-bottom: 1px solid <?= e($theme['border']) ?>;
            background: <?= e($theme['tabBg']) ?>; overflow-x: auto;
        }
        .tabs a {
            flex: 1 0 auto; padding: 9px 10px; font-size: 11px;
            font-weight: 600; white-space: nowrap; text-align: center;
            border-bottom: 2px solid transparent;
            color: <?= e($theme['textFaint']) ?>;
        }
        .tabs a.active {
            background: <?= e($theme['tabActive']) ?>;
            color: <?= e($theme['tabText']) ?>;
            border-bottom-color: <?= e($theme['tabText']) ?>;
        }
        .content { padding: 14px; max-width: 900px; margin: 0 auto; }
        .diagram-wrap {
            flex: 1; overflow: hidden; position: relative; cursor: grab;
            user-select: none; -webkit-user-select: none;
        }
        .diagram-wrap.grabbing { cursor: grabbing; }
        .diagram-wrap svg { width: 100%; height: 100%; display: block; }
        .disease-card {
            margin-bottom: 10px; padding: 14px; border-radius: 8px; cursor: pointer;
        }
        .disease-card:hover { opacity: 0.9; }
        .badge {
            font-size: 9px; font-weight: 700; color: #fff;
            padding: 2px 6px; border-radius: 3px; display: inline-block;
        }
        .topic-btn {
            border-radius: 4px; padding: 3px 6px; cursor: pointer;
            font-size: 9px; font-weight: 600; border-width: 1px; border-style: solid;
            display: inline-block; margin: 2px;
        }
        .bio-table { margin-top: 16px; padding: 14px; border-radius: 8px; }
        .bio-row { padding: 5px 8px; margin-bottom: 3px; border-radius: 4px; }
    </style>
</head>
<body>

<div class="header">
    <?php
    $langKeys = array_keys(LANGUAGES);
    $nextLang = $langKeys[(array_search($lang, $langKeys, true) + 1) % count($langKeys)];
    ?>
    <a href="<?= e(url(['lang' => $nextLang])) ?>" class="lang-toggle">
        <?= e(LANGUAGES[$nextLang]['flag']) ?>
    </a>
    <a href="<?= e(url(['dark' => $dark ? '0' : '1'])) ?>" class="theme-toggle" data-target-theme="<?= $dark ? 'light' : 'dark' ?>">
        <?= $theme['toggleIcon'] ?>
    </a>
    <h1><?= $lang === 'en' ? 'Immune System · Signaling Pathways · Autoimmune Diseases' : 'Immunsystem · Signalwege · Autoimmunerkrankungen' ?></h1>
    <p><?= $lang === 'en'
        ? 'Each column = one signaling pathway · Arrows ↓ = signal flow · Click any element for detailed explanation'
        : 'Jede Spalte = ein Signalweg · Pfeile ↓ = Signalfluss · Klick auf jedes Element für ausführliche Erklärung' ?></p>
</div>

<div class="tabs">
    <?php
    $tabLabels = $lang === 'en'
        ? ['diagram' => '⬡ Pathways', 'diseases' => '⚕ Diseases & Biologics', 'detail' => '◉ Detail', 'sources' => '📚 Sources']
        : ['diagram' => '⬡ Signalwege', 'diseases' => '⚕ Erkrankungen & Biologika', 'detail' => '◉ Detail', 'sources' => '📚 Quellen'];
    if ($info) {
        $short = mb_substr($info['title'], 0, 22);
        $tabLabels['detail'] = "◉ {$short}…";
    }
    foreach ($tabLabels as $id => $label): ?>
        <a href="<?= e(url(['tab' => $id])) ?>" class="<?= $tab === $id ? 'active' : '' ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
</div>

<?php if ($tab === 'diagram'): ?>
    <?php include __DIR__ . '/tab_diagram.php'; ?>
<?php elseif ($tab === 'diseases'): ?>
    <?php include __DIR__ . '/tab_diseases.php'; ?>
<?php elseif ($tab === 'detail'): ?>
    <?php include __DIR__ . '/tab_detail.php'; ?>
<?php elseif ($tab === 'sources'): ?>
    <?php include __DIR__ . '/tab_sources.php'; ?>
<?php endif; ?>

<script>
// Theme toggle via cookie (no page reload needed for subsequent visits)
document.querySelector('.theme-toggle')?.addEventListener('click', function(e) {
    e.preventDefault();
    var target = this.getAttribute('data-target-theme');
    document.cookie = 'theme=' + target + ';path=/;max-age=31536000';
    location.reload();
});
</script>

</body>
</html>
