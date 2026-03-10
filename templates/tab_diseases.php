<?php
/** @var array $theme */
/** @var array $topics */
/** @var string $lang */
$isDark = ($theme === THEME_DARK);
$diseaseKeys = get_disease_keys();
$biologics = get_biologics($lang);
$labelInd = $lang === 'en' ? 'Indications' : 'Indikationen';
$labelRisk = $lang === 'en' ? 'Risks' : 'Risiken';
$labelBio = $lang === 'en' ? 'BIOLOGICS & JAK INHIBITORS' : 'BIOLOGIKA & JAK-INHIBITOREN';
$labelFull = $lang === 'en' ? '→ full explanation' : '→ vollständige Erklärung';
$labelDisease = $lang === 'en' ? 'DISEASE' : 'ERKRANKUNG';
?>
<div class="content">
    <?php foreach ($diseaseKeys as $k):
        if (!isset($topics[$k])) continue;
        $d = $topics[$k];
        $dUrl = url(['tab' => 'detail', 'sel' => $k]);
    ?>
        <a href="<?= e($dUrl) ?>">
            <div class="disease-card" style="background:<?= e($d['color']) . $theme['cardBg'] ?>;border:1px solid <?= e($d['color']) ?>30">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                    <span class="badge" style="background:<?= e($d['color']) ?>"><?= e($labelDisease) ?></span>
                    <span style="font-size:14px;font-weight:700;color:<?= e($d['color']) ?>"><?= e($d['title']) ?></span>
                </div>
                <div style="font-size:11.5px;line-height:1.6;color:<?= e($theme['textMuted']) ?>">
                    <?= e(mb_substr($d['text'], 0, 350)) ?>…
                    <span style="color:<?= e($d['color']) ?>;font-weight:600"><?= e($labelFull) ?></span>
                </div>
            </div>
        </a>
    <?php endforeach; ?>

    <!-- Biologics table -->
    <div class="bio-table" style="background:<?= $isDark ? 'rgba(68,204,68,.05)' : 'rgba(20,140,20,.06)' ?>;border:1px solid <?= $isDark ? 'rgba(68,204,68,.18)' : 'rgba(20,140,20,.2)' ?>">
        <div style="font-size:10px;font-weight:700;color:<?= e($theme['drugBorder']) ?>;margin-bottom:10px;letter-spacing:1px">
            <?= e($labelBio) ?>
        </div>
        <?php foreach ($biologics as $b): ?>
            <div class="bio-row" style="background:<?= e($b['c']) ?>05;border:1px solid <?= e($b['c']) ?>12">
                <span style="font-size:10px;font-weight:800;color:<?= e($b['c']) ?>"><?= e($b['t']) ?>: </span>
                <span style="font-size:9.5px;color:<?= e($theme['drugLabel']) ?>"><?= e($b['d']) ?></span>
                <div style="font-size:8.5px;color:<?= e($theme['drugText']) ?>;margin-top:1px">
                    <span style="color:<?= $isDark ? '#88ccee' : '#1155aa' ?>"><?= e($labelInd) ?>:</span> <?= e($b['i']) ?>
                    · <span style="color:<?= $isDark ? '#ddaa77' : '#994400' ?>"><?= e($labelRisk) ?>:</span> <?= e($b['r']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
