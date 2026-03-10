<?php
/** @var array $theme */
/** @var string $lang */
$sources = get_sources($lang);
$heading = $lang === 'en' ? 'Scientific Sources' : 'Wissenschaftliche Quellen';
?>
<div class="content" style="max-width:800px">
    <h3 style="font-size:14px;color:<?= e($theme['srcCat']) ?>;margin-top:0"><?= e($heading) ?></h3>
    <?php foreach ($sources as $section): ?>
        <div style="margin-bottom:12px">
            <div style="font-size:10px;font-weight:700;color:<?= e($theme['srcCat']) ?>;margin-bottom:4px">
                <?= e($section['c']) ?>
            </div>
            <?php foreach ($section['r'] as $i => $ref): ?>
                <div style="padding-left:10px;font-size:10px;color:<?= e($theme['srcText']) ?>;line-height:1.5">
                    <?= ($i + 1) ?>. <?= e($ref) ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
