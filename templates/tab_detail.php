<?php
/** @var array|null $info */
/** @var array $theme */
/** @var array $topics */
/** @var string|null $sel */
/** @var string $lang */
$labelAll = $lang === 'en' ? 'ALL TOPICS (click for details)' : 'ALLE THEMEN (klick für Details)';
$labelEmpty = $lang === 'en' ? 'Click an element in the diagram for a detailed explanation.' : 'Klicke im Diagramm auf ein Element für die ausführliche Erklärung.';
?>
<div class="content" style="max-width:820px">
    <?php if ($info): ?>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap">
            <span class="badge" style="background:<?= e(CATEGORY_COLORS[$info['cat']] ?? '#666') ?>">
                <?= e(mb_strtoupper($info['cat'])) ?>
            </span>
            <h2 style="margin:0;font-size:16px;font-weight:800;color:<?= e($info['color']) ?>">
                <?= e($info['title']) ?>
            </h2>
        </div>
        <div style="font-size:12.5px;line-height:1.85;color:<?= e($theme['textBody']) ?>">
            <?= render_text($info['text'], $theme) ?>
        </div>
        <div style="margin-top:20px;padding-top:14px;border-top:1px solid <?= e($theme['border']) ?>">
            <div style="font-size:9px;font-weight:700;color:<?= e($theme['textFaint']) ?>;margin-bottom:6px;letter-spacing:1px">
                <?= e($labelAll) ?>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:4px">
                <?php foreach ($topics as $k => $v):
                    if ($k === $sel) continue;
                    $short = mb_strlen($v['title']) > 22 ? mb_substr($v['title'], 0, 22) . '…' : $v['title'];
                ?>
                    <a href="<?= e(url(['tab' => 'detail', 'sel' => $k])) ?>" class="topic-btn"
                       style="background:<?= e($v['color']) ?>0a;border-color:<?= e($v['color']) ?>20;color:<?= e($v['color']) ?>">
                        <?= e($short) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align:center;padding:50px;color:<?= e($theme['emptyText']) ?>">
            <?= e($labelEmpty) ?>
        </div>
    <?php endif; ?>
</div>
