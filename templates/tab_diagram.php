<?php
/** @var array $theme */
/** @var array $columns */
/** @var array $rows */
/** @var string $lang */
$R = $rows;
$diseases = get_diagram_diseases($lang);
$isDark = ($theme === THEME_DARK);
?>
<div class="diagram-wrap">
<svg viewBox="0 0 1120 700">
    <defs>
        <marker id="ad" markerWidth="8" markerHeight="6" refX="8" refY="3" orient="auto">
            <path d="M0,0 L8,3 L0,6" fill="<?= e($theme['svgArrow']) ?>" fill-opacity=".8"/>
        </marker>
        <marker id="ar" markerWidth="8" markerHeight="6" refX="8" refY="3" orient="auto">
            <path d="M0,0 L8,3 L0,6" fill="#ff4444" fill-opacity=".7"/>
        </marker>
    </defs>

    <!-- Row backgrounds -->
    <?php
    $rowBgs = [
        ['y' => $R['cells'] - 28, 'h' => 56, 'c' => '68,136,255'],
        ['y' => $R['cyt'] - 16, 'h' => 34, 'c' => '238,119,34'],
        ['y' => $R['rec'] - 16, 'h' => 34, 'c' => '17,170,153'],
        ['y' => $R['jak'] - 16, 'h' => 34, 'c' => '51,102,204'],
        ['y' => $R['stat'] - 16, 'h' => 34, 'c' => '136,136,170'],
        ['y' => $R['target'] - 20, 'h' => 42, 'c' => '204,68,170'],
        ['y' => $R['drug'] - 20, 'h' => 42, 'c' => '68,204,68'],
        ['y' => $R['disease'] - 18, 'h' => 70, 'c' => '255,100,100'],
    ];
    foreach ($rowBgs as $rb): ?>
        <rect x="45" y="<?= $rb['y'] ?>" width="1065" height="<?= $rb['h'] ?>" rx="4"
              fill="rgba(<?= $rb['c'] ?>,<?= $theme['svgRowBg'] ?>)"/>
    <?php endforeach; ?>

    <!-- Row labels -->
    <?php
    $rowLabels = $lang === 'en'
        ? [
            ['y' => $R['cells'], 't' => 'CELLS', 'c' => '#4488ff'],
            ['y' => $R['cyt'], 't' => 'CYTOKINES', 'c' => '#ee7722'],
            ['y' => $R['rec'], 't' => 'RECEPTORS', 'c' => '#11aa99'],
            ['y' => $R['jak'], 't' => 'JAKs', 'c' => '#3366cc'],
            ['y' => $R['stat'], 't' => 'STATs', 'c' => '#8888aa'],
            ['y' => $R['target'], 't' => 'TARGET TISSUE', 'c' => '#cc44aa'],
            ['y' => $R['drug'], 't' => 'THERAPY', 'c' => '#44cc44'],
            ['y' => $R['disease'], 't' => 'DISEASES', 'c' => '#ff6666'],
        ]
        : [
            ['y' => $R['cells'], 't' => 'ZELLEN', 'c' => '#4488ff'],
            ['y' => $R['cyt'], 't' => 'ZYTOKINE', 'c' => '#ee7722'],
            ['y' => $R['rec'], 't' => 'REZEPTOREN', 'c' => '#11aa99'],
            ['y' => $R['jak'], 't' => 'JAKs', 'c' => '#3366cc'],
            ['y' => $R['stat'], 't' => 'STATs', 'c' => '#8888aa'],
            ['y' => $R['target'], 't' => 'ZIELGEWEBE', 'c' => '#cc44aa'],
            ['y' => $R['drug'], 't' => 'THERAPIE', 'c' => '#44cc44'],
            ['y' => $R['disease'], 't' => 'KRANKHEITEN', 'c' => '#ff6666'],
        ];
    foreach ($rowLabels as $rl): ?>
        <text x="8" y="<?= $rl['y'] + 3 ?>" font-size="6" fill="<?= e($rl['c']) ?>"
              fill-opacity="<?= $isDark ? '.7' : '.8' ?>" font-weight="700" letter-spacing="1"><?= e($rl['t']) ?></text>
    <?php endforeach; ?>

    <!-- Direction labels -->
    <?php
    $dirLabels = $lang === 'en'
        ? [
            ['y' => $R['cyt'] - 20, 't' => 'produces ↓'],
            ['y' => $R['rec'] - 20, 't' => 'binds to ↓'],
            ['y' => $R['jak'] - 20, 't' => 'activates ↓'],
            ['y' => $R['stat'] - 20, 't' => 'activates ↓'],
            ['y' => $R['target'] - 24, 't' => 'acts on ↓'],
            ['y' => $R['drug'] - 24, 't' => 'blocked by ↓'],
        ]
        : [
            ['y' => $R['cyt'] - 20, 't' => 'produziert ↓'],
            ['y' => $R['rec'] - 20, 't' => 'bindet an ↓'],
            ['y' => $R['jak'] - 20, 't' => 'aktiviert ↓'],
            ['y' => $R['stat'] - 20, 't' => 'aktiviert ↓'],
            ['y' => $R['target'] - 24, 't' => 'wirkt auf ↓'],
            ['y' => $R['drug'] - 24, 't' => 'blockiert durch ↓'],
        ];
    $dirFill = $isDark ? '#aabbcc' : '#445566';
    foreach ($dirLabels as $dl): ?>
        <text x="30" y="<?= $dl['y'] ?>" font-size="5" fill="<?= $dirFill ?>"
              fill-opacity=".7" font-weight="600"><?= e($dl['t']) ?></text>
    <?php endforeach; ?>

    <!-- Columns -->
    <?php foreach ($columns as $ci => $col):
        $x = $col['x'];
        $detailUrl = url(['tab' => 'detail', 'sel' => $col['cellKey'] ?? $col['ck']]);
        $cytUrl = url(['tab' => 'detail', 'sel' => $col['ck']]);
    ?>
        <!-- Guide line -->
        <line x1="<?= $x ?>" y1="<?= $R['cyt'] - 10 ?>" x2="<?= $x ?>" y2="<?= $R['drug'] + 18 ?>"
              stroke="<?= e($col['cc']) ?>" stroke-width="<?= $theme['guideLineWidth'] ?>"
              stroke-opacity="<?= $theme['guideLineOpacity'] ?>"/>

        <?php if ($col['cells']): ?>
            <!-- Cell node -->
            <a href="<?= e($detailUrl) ?>">
                <circle cx="<?= $x ?>" cy="<?= $R['cells'] ?>" r="19"
                        fill="<?= e($col['cc']) . $theme['nodeAlpha'] ?>"
                        stroke="<?= e($col['cc']) ?>" stroke-width="<?= $theme['cellStrokeWidth'] ?>"/>
                <text x="<?= $x ?>" y="<?= $R['cells'] - 3 ?>" text-anchor="middle"
                      font-size="13" fill="<?= e($col['cc']) ?>"><?= $col['icon'] ?></text>
                <text x="<?= $x ?>" y="<?= $R['cells'] + 12 ?>" text-anchor="middle"
                      font-size="7" fill="<?= e($col['cc']) ?>" font-weight="600"><?= e($col['cells']) ?></text>
            </a>
            <!-- Arrow: cell → cytokine -->
            <line x1="<?= $x ?>" y1="<?= $R['cells'] + 22 ?>" x2="<?= $x ?>" y2="<?= $R['cyt'] - 14 ?>"
                  stroke="<?= e($col['cc']) ?>" stroke-width="<?= $theme['arrowWidth'] ?>"
                  stroke-opacity="<?= $theme['arrowOpacity'] ?>" marker-end="url(#ad)"/>
        <?php endif; ?>

        <!-- Cytokine pill -->
        <a href="<?= e($cytUrl) ?>">
            <rect x="<?= $x - 38 ?>" y="<?= $R['cyt'] - 11 ?>" width="76" height="22" rx="11"
                  fill="<?= e($col['cc']) . $theme['cytAlpha'] ?>"
                  stroke="<?= e($col['cc']) ?>" stroke-width="<?= $theme['cytStrokeWidth'] ?>"/>
            <text x="<?= $x ?>" y="<?= $R['cyt'] + 4 ?>" text-anchor="middle"
                  font-size="8" fill="<?= e($col['cc']) ?>" font-weight="700"><?= e($col['cyt']) ?></text>
        </a>

        <!-- Arrow: cytokine → receptor (dashed) -->
        <line x1="<?= $x ?>" y1="<?= $R['cyt'] + 13 ?>" x2="<?= $x ?>" y2="<?= $R['rec'] - 14 ?>"
              stroke="<?= e($col['cc']) ?>" stroke-width="<?= $theme['arrowWidth'] ?>"
              stroke-opacity="<?= $theme['arrowDashOpacity'] ?>" stroke-dasharray="4,3" marker-end="url(#ad)"/>

        <!-- Receptor box -->
        <a href="<?= e($cytUrl) ?>">
            <rect x="<?= $x - 42 ?>" y="<?= $R['rec'] - 12 ?>" width="84" height="24" rx="3"
                  fill="<?= e($col['cc']) . $theme['recAlpha'] ?>"
                  stroke="<?= e($col['cc']) ?>" stroke-width="<?= $theme['recStrokeWidth'] ?>"/>
            <text x="<?= $x ?>" y="<?= $R['rec'] + 4 ?>" text-anchor="middle"
                  font-size="6.5" fill="<?= e($col['cc']) ?>" font-weight="600"><?= e($col['rec']) ?></text>
        </a>

        <!-- Arrow: receptor → JAK -->
        <line x1="<?= $x ?>" y1="<?= $R['rec'] + 14 ?>" x2="<?= $x ?>" y2="<?= $R['jak'] - 14 ?>"
              stroke="<?= e($col['cc']) ?>" stroke-width="<?= $theme['arrowWidth'] ?>"
              stroke-opacity="<?= $theme['arrowOpacity'] ?>" marker-end="url(#ad)"/>

        <!-- JAK box -->
        <a href="<?= e($cytUrl) ?>">
            <rect x="<?= $x - 38 ?>" y="<?= $R['jak'] - 12 ?>" width="76" height="24" rx="3"
                  fill="<?= e($col['cc']) . $theme['jakAlpha'] ?>"
                  stroke="<?= e($col['cc']) ?>" stroke-width="<?= $theme['cytStrokeWidth'] ?>" stroke-dasharray="4,2"/>
            <text x="<?= $x ?>" y="<?= $R['jak'] + 4 ?>" text-anchor="middle"
                  font-size="7" fill="<?= e($col['cc']) ?>" font-weight="700"><?= e($col['jak']) ?></text>
        </a>

        <!-- Arrow: JAK → STAT -->
        <line x1="<?= $x ?>" y1="<?= $R['jak'] + 14 ?>" x2="<?= $x ?>" y2="<?= $R['stat'] - 14 ?>"
              stroke="<?= e($col['cc']) ?>" stroke-width="<?= $theme['arrowWidth'] ?>"
              stroke-opacity="<?= $theme['arrowOpacity'] ?>" marker-end="url(#ad)"/>

        <!-- STAT pill -->
        <a href="<?= e($cytUrl) ?>">
            <rect x="<?= $x - 34 ?>" y="<?= $R['stat'] - 12 ?>" width="68" height="24" rx="12"
                  fill="<?= e($col['cc']) . $theme['statAlpha'] ?>"
                  stroke="<?= e($col['cc']) ?>" stroke-width="<?= $theme['cytStrokeWidth'] ?>"/>
            <text x="<?= $x ?>" y="<?= $R['stat'] + 4 ?>" text-anchor="middle"
                  font-size="7.5" fill="<?= e($col['cc']) ?>" font-weight="700"><?= e($col['stat']) ?></text>
        </a>

        <!-- Arrow: STAT → target -->
        <line x1="<?= $x ?>" y1="<?= $R['stat'] + 14 ?>" x2="<?= $x ?>" y2="<?= $R['target'] - 18 ?>"
              stroke="<?= e($col['cc']) ?>" stroke-width="<?= $theme['arrowWidth'] ?>"
              stroke-opacity="<?= $theme['arrowDashOpacity'] ?>" marker-end="url(#ad)"/>

        <!-- Target box -->
        <a href="<?= e($cytUrl) ?>">
            <rect x="<?= $x - 44 ?>" y="<?= $R['target'] - 16 ?>" width="88" height="28" rx="4"
                  fill="<?= e($col['cc']) . $theme['targetAlpha'] ?>"
                  stroke="<?= e($col['cc']) ?>" stroke-width="<?= $theme['recStrokeWidth'] ?>"/>
            <text x="<?= $x ?>" y="<?= $R['target'] + 3 ?>" text-anchor="middle"
                  font-size="6.5" fill="<?= e($col['cc']) ?>" font-weight="600"><?= e($col['target']) ?></text>
        </a>

        <!-- Drug box -->
        <a href="<?= e($cytUrl) ?>">
            <rect x="<?= $x - 48 ?>" y="<?= $R['drug'] - 18 ?>" width="96" height="34" rx="4"
                  fill="<?= $theme['drugBg'] ?>"
                  stroke="<?= e($theme['drugBorder']) ?>" stroke-width="<?= $theme['recStrokeWidth'] ?>"/>
            <text x="<?= $x ?>" y="<?= $R['drug'] ?>" text-anchor="middle"
                  font-size="7.5" fill="<?= e($theme['drugBorder']) ?>" font-weight="700"><?= e($col['drug']) ?></text>
            <text x="<?= $x ?>" y="<?= $R['drug'] + 11 ?>" text-anchor="middle"
                  font-size="5.5" fill="<?= $isDark ? '#44dd4488' : '#22883388' ?>"><?= e($col['dsub']) ?></text>
        </a>
    <?php endforeach; ?>

    <!-- Special: Paradoxical psoriasis -->
    <rect x="130" y="<?= $R['drug'] + 24 ?>" width="200" height="20" rx="3"
          fill="<?= $isDark ? '#ff444418' : '#ff444415' ?>" stroke="#ff4444" stroke-width="1" stroke-dasharray="4,2"/>
    <text x="230" y="<?= $R['drug'] + 37 ?>" text-anchor="middle" font-size="6.5"
          fill="<?= $isDark ? '#ff8888' : '#cc3333' ?>" font-weight="700">
        <?= $lang === 'en'
            ? '⚠ Anti-TNF → pDC stays immature → IFN-α↑↑ → Paradoxical Psoriasis'
            : '⚠ Anti-TNF → pDC bleibt unreif → IFN-α↑↑ → Paradoxe Psoriasis' ?>
    </text>
    <path d="M 80 <?= $R['drug'] + 18 ?> Q 80 <?= $R['drug'] + 34 ?> 130 <?= $R['drug'] + 34 ?>"
          fill="none" stroke="#ff4444" stroke-width="1" stroke-dasharray="4,2" stroke-opacity=".6"/>
    <path d="M 330 <?= $R['drug'] + 34 ?> Q 370 <?= $R['drug'] + 34 ?> 370 <?= $R['drug'] + 18 ?>"
          fill="none" stroke="#ff4444" stroke-width="1" stroke-dasharray="4,2" stroke-opacity=".6"/>

    <!-- TNF→pDC feedback -->
    <path d="M 80 <?= $R['cyt'] - 15 ?> Q 50 <?= $R['cells'] - 10 ?> 370 <?= $R['cells'] + 22 ?>"
          fill="none" stroke="#ff4444" stroke-width=".9" stroke-dasharray="4,3"
          stroke-opacity="<?= $isDark ? '.5' : '.6' ?>"/>
    <text x="185" y="<?= $R['cells'] + 30 ?>" font-size="6"
          fill="<?= $isDark ? '#ff9999' : '#bb2222' ?>" font-weight="600">
        <?= $lang === 'en'
            ? 'TNF-α → pDC maturation (⊣ brakes IFN-α)'
            : 'TNF-α → pDC-Reifung (⊣ bremst IFN-α)' ?>
    </text>

    <!-- Keratinocyte feedback -->
    <path d="M 655 <?= $R['target'] + 14 ?> Q 680 <?= $R['target'] + 45 ?> 500 <?= $R['target'] + 45 ?> Q 370 <?= $R['target'] + 45 ?> 370 <?= $R['cells'] + 22 ?>"
          fill="none" stroke="#dd5500" stroke-width=".7" stroke-dasharray="3,2"
          stroke-opacity="<?= $isDark ? '.45' : '.55' ?>"/>
    <text x="530" y="<?= $R['target'] + 52 ?>" font-size="5.5"
          fill="<?= $isDark ? '#ee9955' : '#884400' ?>" font-weight="500">
        <?= $lang === 'en'
            ? 'Keratinocytes → AMPs → pDC (Feedback Loop Psoriasis)'
            : 'Keratinozyten → AMPs → pDC (Feedback-Loop Psoriasis)' ?>
    </text>

    <!-- Disease row 1 -->
    <?php foreach ($diseases['row1'] as $d):
        $dUrl = url(['tab' => 'detail', 'sel' => $d['k']]);
    ?>
        <a href="<?= e($dUrl) ?>">
            <rect x="<?= $d['x'] - $d['w'] / 2 ?>" y="<?= $R['disease'] - 13 ?>"
                  width="<?= $d['w'] ?>" height="28" rx="14"
                  fill="<?= e($d['c']) . ($isDark ? '18' : '20') ?>"
                  stroke="<?= e($d['c']) ?>" stroke-width="1.2"/>
            <text x="<?= $d['x'] ?>" y="<?= $R['disease'] + 5 ?>" text-anchor="middle"
                  font-size="7.5" fill="<?= e($d['c']) ?>" font-weight="700"><?= e($d['l']) ?></text>
        </a>
    <?php endforeach; ?>

    <!-- Disease row 2 -->
    <?php foreach ($diseases['row2'] as $d):
        $dUrl = url(['tab' => 'detail', 'sel' => $d['k']]);
    ?>
        <a href="<?= e($dUrl) ?>">
            <rect x="<?= $d['x'] - $d['w'] / 2 ?>" y="<?= $R['disease'] + 22 ?>"
                  width="<?= $d['w'] ?>" height="22" rx="11"
                  fill="<?= e($d['c']) . ($isDark ? '12' : '15') ?>"
                  stroke="<?= e($d['c']) ?>" stroke-width=".8"/>
            <text x="<?= $d['x'] ?>" y="<?= $R['disease'] + 36 ?>" text-anchor="middle"
                  font-size="6.5" fill="<?= e($d['c']) ?>" font-weight="600"><?= e($d['l']) ?></text>
        </a>
    <?php endforeach; ?>

    <!-- Legend -->
    <g transform="translate(55,680)">
        <?php
        $legendItems = [
            ['c' => '#ff4444', 'l' => 'TNF/NF-κB'],
            ['c' => '#ee7722', 'l' => 'IL-23/IL-17'],
            ['c' => '#dd5500', 'l' => 'IFN-α'],
            ['c' => '#4488ff', 'l' => 'JAK1'],
            ['c' => '#22bb55', 'l' => 'JAK2'],
            ['c' => '#9955cc', 'l' => 'JAK3'],
            ['c' => '#11aa99', 'l' => 'TYK2'],
            ['c' => '#9944cc', 'l' => ($lang === 'en' ? 'B cells' : 'B-Zellen')],
            ['c' => '#44cc44', 'l' => ($lang === 'en' ? '= Biologic' : '= Biologikum')],
        ];
        foreach ($legendItems as $li => $item): ?>
            <g transform="translate(<?= $li * 115 ?>,0)">
                <rect x="0" y="-4" width="7" height="7" rx="2" fill="<?= e($item['c']) ?>" fill-opacity=".5"/>
                <text x="10" y="3" font-size="6.5" fill="<?= e($theme['legendText']) ?>"><?= e($item['l']) ?></text>
            </g>
        <?php endforeach; ?>
    </g>
</svg>
</div>
