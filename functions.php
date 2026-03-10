<?php

declare(strict_types=1);

/**
 * Parse a markdown file with YAML-like frontmatter.
 * Format:
 * ---
 * title: ...
 * category: ...
 * color: ...
 * ---
 * Body text...
 */
function parse_topic_file(string $path): ?array
{
    if (!file_exists($path)) {
        return null;
    }

    $raw = file_get_contents($path);
    if ($raw === false) {
        return null;
    }

    // Split frontmatter and body
    if (!preg_match('/\A---\s*\n(.*?)\n---\s*\n(.*)\z/s', $raw, $m)) {
        return null;
    }

    $meta = [];
    foreach (explode("\n", trim($m[1])) as $line) {
        if (str_contains($line, ':')) {
            [$key, $val] = explode(':', $line, 2);
            $meta[trim($key)] = trim($val, " \t\n\r\"'");
        }
    }

    return [
        'title' => $meta['title'] ?? '',
        'cat' => $meta['category'] ?? '',
        'color' => $meta['color'] ?? '#888888',
        'text' => trim($m[2]),
    ];
}

/**
 * Load all topic markdown files for a language.
 */
function load_all_topics(string $lang): array
{
    $base = __DIR__ . "/content/{$lang}";
    $topics = [];

    if (!is_dir($base)) {
        return $topics;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->getExtension() !== 'md') {
            continue;
        }
        $key = $file->getBasename('.md');
        $data = parse_topic_file($file->getPathname());
        if ($data) {
            $topics[$key] = $data;
        }
    }

    return $topics;
}

/**
 * Render markdown-like text to HTML.
 * Supports **bold** and paragraph breaks.
 */
function render_text(string $text, array $theme): string
{
    $html = '';
    $paragraphs = explode("\n\n", $text);

    foreach ($paragraphs as $para) {
        $para = trim($para);
        if ($para === '') {
            continue;
        }
        $escaped = htmlspecialchars($para, ENT_QUOTES, 'UTF-8');
        // Bold
        $escaped = preg_replace(
            '/\*\*(.*?)\*\*/',
            '<strong style="color:' . e($theme['bold']) . '">$1</strong>',
            $escaped
        );
        $html .= '<p style="margin:0 0 10px">' . nl2br($escaped) . '</p>';
    }

    return $html;
}

/**
 * Escape for HTML attribute output.
 */
function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/**
 * Build a URL preserving current query params but overriding given ones.
 */
function url(array $params): string
{
    $current = $_GET;
    $merged = array_merge($current, $params);
    // Remove null values
    $merged = array_filter($merged, fn($v) => $v !== null);
    return '?' . http_build_query($merged);
}

/**
 * Get column layout data for the SVG diagram.
 */
function get_columns(): array
{
    return [
        ['x' => 80, 'cells' => 'Makrophage', 'icon' => "\xF0\x9F\x94\xAC", 'cyt' => 'TNF-\u03B1', 'rec' => 'TNFR1/2', 'jak' => "TRAF2\u2192IKK", 'stat' => "NF-\u03BAB", 'target' => 'FLS / Synovium', 'drug' => "Anti-TNF-\u03B1", 'dsub' => 'Infliximab etc.', 'cc' => '#ff4444', 'ck' => 'tnf_pathway', 'cellKey' => 'macrophage'],
        ['x' => 175, 'cells' => 'cDC', 'icon' => "\xF0\x9F\x8C\x9F", 'cyt' => 'IL-12', 'rec' => "IL-12R\u03B21/\u03B22", 'jak' => 'JAK2 + TYK2', 'stat' => 'STAT4', 'target' => 'Th1-Differenz.', 'drug' => 'Ustekinumab', 'dsub' => 'Anti-IL-12/23', 'cc' => '#ffaa00', 'ck' => 'tyk2', 'cellKey' => 'dc'],
        ['x' => 270, 'cells' => null, 'icon' => null, 'cyt' => 'IL-23', 'rec' => "IL-23R/12R\u03B21", 'jak' => 'JAK2 + TYK2', 'stat' => 'STAT3/4', 'target' => 'Th17-Expansion', 'drug' => 'Anti-IL-23(p19)', 'dsub' => 'Risankizumab etc.', 'cc' => '#ee7722', 'ck' => 'il23_il17', 'cellKey' => null],
        ['x' => 370, 'cells' => 'pDC', 'icon' => "\u2726", 'cyt' => "IFN-\u03B1", 'rec' => 'IFNAR1/2', 'jak' => 'JAK1 + TYK2', 'stat' => 'STAT1/2', 'target' => 'ISG-Expression', 'drug' => 'TYK2i / Anti-IFNAR', 'dsub' => 'Deucravacitinib', 'cc' => '#dd5500', 'ck' => 'ifn_pathway', 'cellKey' => 'dc'],
        ['x' => 465, 'cells' => 'Th1', 'icon' => "\u2694", 'cyt' => "IFN-\u03B3", 'rec' => "IFN-\u03B3R", 'jak' => 'JAK1 + JAK2', 'stat' => 'STAT1', 'target' => 'Makr.-Aktivierung', 'drug' => 'JAK1i', 'dsub' => 'Upadacitinib', 'cc' => '#4488ff', 'ck' => 'jak1', 'cellKey' => 'th1'],
        ['x' => 560, 'cells' => 'Th2', 'icon' => "\xF0\x9F\x9B\xA1", 'cyt' => 'IL-4 / IL-13', 'rec' => "IL-4R\u03B1 / \u03B3c", 'jak' => 'JAK1 + JAK3', 'stat' => 'STAT6', 'target' => 'IgE-Switch', 'drug' => 'Dupilumab', 'dsub' => "Anti-IL-4R\u03B1", 'cc' => '#44cc77', 'ck' => 'th2', 'cellKey' => 'th2'],
        ['x' => 655, 'cells' => 'Th17', 'icon' => "\xF0\x9F\x94\xA5", 'cyt' => 'IL-17A/F', 'rec' => 'IL-17RA/RC', 'jak' => "ACT1\u2192NF-\u03BAB", 'stat' => "NF-\u03BAB/MAPK", 'target' => 'Keratinozyten', 'drug' => 'Anti-IL-17A', 'dsub' => 'Secukinumab', 'cc' => '#ee8833', 'ck' => 'il23_il17', 'cellKey' => 'th17'],
        ['x' => 750, 'cells' => null, 'icon' => null, 'cyt' => 'IL-6', 'rec' => 'IL-6R/gp130', 'jak' => 'JAK1 + JAK2', 'stat' => 'STAT3', 'target' => 'FLS + Osteoklasten', 'drug' => 'Tocilizumab', 'dsub' => 'Anti-IL-6R', 'cc' => '#ffcc33', 'ck' => 'jak2', 'cellKey' => null],
        ['x' => 845, 'cells' => 'Treg', 'icon' => "\u2696", 'cyt' => 'IL-2', 'rec' => "IL-2R / \u03B3c", 'jak' => 'JAK1 + JAK3', 'stat' => 'STAT5', 'target' => 'T-Proliferation', 'drug' => 'Tofacitinib', 'dsub' => 'JAK1/3i', 'cc' => '#22aa88', 'ck' => 'jak3', 'cellKey' => 'treg'],
        ['x' => 940, 'cells' => 'B-Zelle', 'icon' => "\xF0\x9F\x92\x8E", 'cyt' => 'BAFF / APRIL', 'rec' => 'BAFF-R/TACI', 'jak' => 'NF-\u03BAB2 / NIK', 'stat' => 'p52 / RelB', 'target' => "B-Zell-\u00DCberleben", 'drug' => 'Rituximab + Belimumab', 'dsub' => 'Anti-CD20+Anti-BAFF', 'cc' => '#9944cc', 'ck' => 'bcell', 'cellKey' => 'bcell'],
        ['x' => 1040, 'cells' => 'Neutrophil', 'icon' => "\u26A1", 'cyt' => 'RANKL', 'rec' => 'RANK', 'jak' => "TRAF6\u2192NF-\u03BAB", 'stat' => 'NFATc1', 'target' => 'Osteoklasten', 'drug' => 'Denosumab', 'dsub' => 'Anti-RANKL', 'cc' => '#aa6633', 'ck' => 'osteoclast', 'cellKey' => 'neutrophil'],
    ];
}

/**
 * Row Y positions for the SVG diagram.
 */
function get_rows(): array
{
    return [
        'cells' => 55,
        'cyt' => 145,
        'rec' => 220,
        'jak' => 290,
        'stat' => 360,
        'target' => 435,
        'drug' => 510,
        'disease' => 590,
    ];
}

/**
 * Get biologics table data.
 */
function get_biologics(string $lang): array
{
    if ($lang === 'en') {
        return [
            ['t' => "TNF-\u03B1", 'd' => 'Infliximab, Adalimumab, Etanercept, Certolizumab, Golimumab', 'i' => 'RA, PsA, Pso, SpA, IBD', 'r' => 'Paradoxical psoriasis (2-5%), TB reactivation, Lupus-like', 'c' => '#ff4444'],
            ['t' => 'IL-6R', 'd' => 'Tocilizumab, Sarilumab', 'i' => 'RA, JIA, Giant cell arteritis', 'r' => "Neutropenia, liver enzymes\u2191", 'c' => '#ffcc33'],
            ['t' => 'IL-23(p19)', 'd' => 'Risankizumab, Guselkumab, Tildrakizumab', 'i' => "Pso, PsA, Crohn's", 'r' => 'Good safety profile', 'c' => '#ee7722'],
            ['t' => 'IL-12/23(p40)', 'd' => 'Ustekinumab', 'i' => 'Pso, PsA, IBD', 'r' => 'Well tolerated', 'c' => '#ffaa00'],
            ['t' => 'IL-17A', 'd' => 'Secukinumab, Ixekizumab', 'i' => 'Pso, PsA, SpA', 'r' => 'Candida infections, possible IBD worsening', 'c' => '#dd7722'],
            ['t' => "IL-4R\u03B1", 'd' => 'Dupilumab', 'i' => 'AD, Asthma, CRSwNP', 'r' => 'Conjunctivitis', 'c' => '#44cc77'],
            ['t' => 'JAK1', 'd' => 'Upadacitinib, Filgotinib, Abrocitinib', 'i' => 'RA, AD, PsA, SpA, IBD', 'r' => 'Herpes zoster, CV risk (FDA Warning)', 'c' => '#4488ff'],
            ['t' => 'JAK1/2', 'd' => 'Baricitinib, Ruxolitinib', 'i' => 'RA, AD, Alopecia areata, Myelofibrosis', 'r' => 'Anemia, Thrombocytopenia, VTE', 'c' => '#22bb55'],
            ['t' => 'JAK1/3', 'd' => 'Tofacitinib', 'i' => 'RA, PsA, IBD', 'r' => 'MACE+Malignancies >65y (Black Box Warning)', 'c' => '#9955cc'],
            ['t' => 'TYK2', 'd' => 'Deucravacitinib', 'i' => 'Psoriasis', 'r' => 'Best JAKi safety profile', 'c' => '#11aa99'],
            ['t' => 'CD20', 'd' => 'Rituximab, Ocrelizumab', 'i' => "RA, SLE, Sj\u00F6gren's, MS", 'r' => 'PML (rare), Hypogammaglobulinemia', 'c' => '#9944cc'],
            ['t' => 'BAFF', 'd' => 'Belimumab, Ianalumab', 'i' => "SLE, Sj\u00F6gren's", 'r' => 'Infections', 'c' => '#aa44cc'],
            ['t' => 'IFNAR', 'd' => 'Anifrolumab', 'i' => 'SLE', 'r' => 'Herpes zoster', 'c' => '#dd5500'],
        ];
    }

    return [
        ['t' => "TNF-\u03B1", 'd' => 'Infliximab, Adalimumab, Etanercept, Certolizumab, Golimumab', 'i' => 'RA, PsA, Pso, SpA, CED', 'r' => 'Paradoxe Psoriasis (2-5%), TB-Reaktivierung, Lupus-like', 'c' => '#ff4444'],
        ['t' => 'IL-6R', 'd' => 'Tocilizumab, Sarilumab', 'i' => 'RA, JIA, Riesenzellarteriitis', 'r' => "Neutropenie, Leberenzym\u2191", 'c' => '#ffcc33'],
        ['t' => 'IL-23(p19)', 'd' => 'Risankizumab, Guselkumab, Tildrakizumab', 'i' => 'Pso, PsA, M.Crohn', 'r' => 'Gutes Sicherheitsprofil', 'c' => '#ee7722'],
        ['t' => 'IL-12/23(p40)', 'd' => 'Ustekinumab', 'i' => 'Pso, PsA, CED', 'r' => "Gut vertr\u00E4glich", 'c' => '#ffaa00'],
        ['t' => 'IL-17A', 'd' => 'Secukinumab, Ixekizumab', 'i' => 'Pso, PsA, SpA', 'r' => "Candida-Infektionen, CED-Verschlechterung m\u00F6glich", 'c' => '#dd7722'],
        ['t' => "IL-4R\u03B1", 'd' => 'Dupilumab', 'i' => 'AD, Asthma, CRSwNP', 'r' => 'Konjunktivitis', 'c' => '#44cc77'],
        ['t' => 'JAK1', 'd' => 'Upadacitinib, Filgotinib, Abrocitinib', 'i' => 'RA, AD, PsA, SpA, CED', 'r' => 'Herpes Zoster, KV-Risiko (FDA Warning)', 'c' => '#4488ff'],
        ['t' => 'JAK1/2', 'd' => 'Baricitinib, Ruxolitinib', 'i' => 'RA, AD, Alopecia areata, Myelofibrose', 'r' => "An\u00E4mie, Thrombozytopenie, VTE", 'c' => '#22bb55'],
        ['t' => 'JAK1/3', 'd' => 'Tofacitinib', 'i' => 'RA, PsA, CED', 'r' => 'MACE+Malignome >65J (Black Box Warning)', 'c' => '#9955cc'],
        ['t' => 'TYK2', 'd' => 'Deucravacitinib', 'i' => 'Psoriasis', 'r' => 'Bestes JAKi-Sicherheitsprofil', 'c' => '#11aa99'],
        ['t' => 'CD20', 'd' => 'Rituximab, Ocrelizumab', 'i' => "RA, SLE, Sj\u00F6gren, MS", 'r' => "PML (selten), Hypogammaglobulin\u00E4mie", 'c' => '#9944cc'],
        ['t' => 'BAFF', 'd' => 'Belimumab, Ianalumab', 'i' => "SLE, Sj\u00F6gren", 'r' => 'Infektionen', 'c' => '#aa44cc'],
        ['t' => 'IFNAR', 'd' => 'Anifrolumab', 'i' => 'SLE', 'r' => 'Herpes Zoster', 'c' => '#dd5500'],
    ];
}

/**
 * Get source references.
 */
function get_sources(string $lang): array
{
    $label_indications = $lang === 'en' ? 'Indications' : 'Indikationen';
    $label_risks = $lang === 'en' ? 'Risks' : 'Risiken';

    return [
        ['c' => 'JAK/STAT-' . ($lang === 'en' ? 'Pathway' : 'Signalweg'), 'r' => [
            'Villarino AV et al. Nat Immunol. 2017;18(4):374-384.',
            'Forbes LR et al. JACI. 2024;154(4):884-896.',
            'Xin P et al. Signal Transduct Target Ther. 2023;8:402.',
            'Hu X et al. Signal Transduct Target Ther. 2021;6:402.',
            'Horesh ME et al. J Exp Med. 2024;221.',
        ]],
        ['c' => ($lang === 'en' ? 'Paradoxical Psoriasis & TNF/IFN-α' : 'Paradoxe Psoriasis & TNF/IFN-α'), 'r' => [
            'Navarini AA et al. Nat Commun. 2018;9(1):25.',
            'Conrad C et al. Front Immunol. 2018;9:2746.',
            'Brown G et al. JAAD. 2017;76(2):334-341.',
            'Afzali A et al. Cureus. 2023;15(8):e43280.',
            'Thibodeaux Q et al. Sci Rep. 2023;13:10389.',
        ]],
        ['c' => ($lang === 'en' ? 'Rheumatoid Arthritis' : 'Rheumatoide Arthritis'), 'r' => [
            'Tanaka Y et al. Int J Mol Sci. 2021;22(20):10922.',
            'Kondo N et al. STTT. 2023;8:68.',
            'Wang Y et al. Nat Commun. 2025;16:2155.',
            'Tran CN et al. Front Med. 2020;7:124.',
            'Norton S et al. Large joints are progressively involved in RA. Rheumatol Int. 2022;42:1053-1061.',
            'Kraan MC et al. Comparison of synovial tissues from knee and small joints. Arthritis Rheum. 2002;46(8):2034-2038.',
            "He J et al. Characteristics of Sj\u00F6gren's in RA. Rheumatology. 2013;52:1084-1089.",
        ]],
        ['c' => ($lang === 'en' ? "Sjögren's Syndrome & B Cells" : 'Sjögren-Syndrom & B-Zellen'), 'r' => [
            'Wang Y et al. Front Immunol. 2021;12:684999.',
            'Bowman SJ et al. JCI Insight. 2022;7(24):e163030.',
            'Fisher BA et al. Rheumatology. 2021;60(vi):vi53-vi63.',
            'Pers JO et al. Arthritis Rheum. 2007;56(5):1464-1477.',
            'Cornec D et al. J Autoimmun. 2012;39(3):161-167.',
            "Andrianopoulou A et al. Musculoskeletal manifestations in Sj\u00F6gren's. Int J Mol Sci. 2021;22(8):3754.",
        ]],
        ['c' => ($lang === 'en' ? 'Atopic Dermatitis' : 'Atopische Dermatitis'), 'r' => [
            'Langan SM et al. Lancet. 2020;396:345-360.',
            'Furue M et al. J Clin Med. 2020;9(11):3741.',
            'Paller AS et al. Front Med. 2024;11:1342176.',
            'Bieber T et al. IL-4 and AD. PMC. 2025.',
        ]],
        ['c' => ($lang === 'en' ? 'Urticaria' : 'Urtikaria'), 'r' => [
            'Maurer M et al. Chronic urticaria: unmet needs. Lancet. 2024;404:738-752.',
            'Maurer M et al. NEJM. 2013;368:924-935 (Omalizumab).',
            'Kolkhir P et al. Allergy. 2022;77:734-766 (EAACI Guideline).',
        ]],
        ['c' => ($lang === 'en' ? "IBD (Crohn's, Colitis)" : 'CED (Crohn, Colitis)'), 'r' => [
            "Torres J et al. Crohn's disease. Lancet. 2017;389:1741-1755.",
            'Kobayashi T et al. Ulcerative colitis. Nat Rev Dis Primers. 2020;6:74.',
            "Sandborn WJ et al. Upadacitinib in Crohn's. NEJM. 2023;388:1966-1980.",
        ]],
        ['c' => ($lang === 'en' ? 'Spondyloarthritis (Ankylosing Spondylitis)' : 'Spondyloarthritis (Bechterew)'), 'r' => [
            'Sieper J, Poddubnyy D. Axial SpA. Lancet. 2017;390:73-84.',
            'van der Heijde D et al. Secukinumab in AS. NEJM. 2015;373:2534-2548.',
        ]],
        ['c' => 'Lupus (SLE, CLE, SCLE)', 'r' => [
            'Tsokos GC. SLE. NEJM. 2011;365:2110-2121.',
            'Morand EF et al. Anifrolumab in SLE (TULIP-2). NEJM. 2020;382:211-221.',
            'Werth VP et al. CLE. Nat Rev Dis Primers. 2024;10:14.',
        ]],
        ['c' => 'MS, GBS, CIDP', 'r' => [
            'Reich DS et al. Multiple Sclerosis. NEJM. 2018;378:169-180.',
            'Hauser SL et al. Ocrelizumab in MS. NEJM. 2017;376:221-234.',
            'Shahrizaila N et al. GBS. Lancet. 2021;397:1214-1228.',
            'Van den Bergh PYK et al. CIDP. J Peripher Nerv Syst. 2021;26:242-268.',
        ]],
        ['c' => ($lang === 'en' ? 'Other Diseases' : 'Weitere Erkrankungen'), 'r' => [
            'Herold KC et al. Teplizumab in T1D. NEJM. 2019;381:603-613.',
            'Joly P et al. Rituximab in Pemphigus. NEJM. 2017;377:545-555.',
            'Jennette JC et al. Vasculitis. NEJM. 2012;367:214-223.',
            'Feldman EL et al. ALS. Lancet. 2022;400:769-786.',
            'Dalakas MC. Inflammatory myopathies. NEJM. 2015;372:1734-1747.',
        ]],
        ['c' => ($lang === 'en' ? 'Textbooks' : 'Lehrbücher'), 'r' => [
            'Abbas AK et al. Cellular and Molecular Immunology. 10th ed. 2022.',
            "Murphy K, Weaver C. Janeway's Immunobiology. 10th ed. 2022.",
        ]],
    ];
}

/**
 * Get disease items for SVG diagram (row 1 and row 2).
 */
function get_diagram_diseases(string $lang): array
{
    if ($lang === 'en') {
        return [
            'row1' => [
                ['x' => 150, 'l' => 'Rheumatoid Arthritis', 'k' => 'ra', 'c' => '#cc6622', 'w' => 130],
                ['x' => 370, 'l' => 'Psoriasis (classic + paradox)', 'k' => 'psoriasis', 'c' => '#dd5500', 'w' => 160],
                ['x' => 580, 'l' => 'Atopic Dermatitis', 'k' => 'ad', 'c' => '#44cc77', 'w' => 120],
                ['x' => 760, 'l' => "Sjögren's Syndrome", 'k' => 'sjogren', 'c' => '#6644aa', 'w' => 110],
                ['x' => 930, 'l' => 'SLE / Lupus', 'k' => 'sle', 'c' => '#cc4488', 'w' => 85],
                ['x' => 1050, 'l' => 'Urticaria', 'k' => 'urticaria', 'c' => '#dd6699', 'w' => 70],
            ],
            'row2' => [
                ['x' => 100, 'l' => "Crohn's", 'k' => 'crohn', 'c' => '#cc7744', 'w' => 70],
                ['x' => 200, 'l' => 'Ulcerative Colitis', 'k' => 'cu', 'c' => '#cc8855', 'w' => 95],
                ['x' => 320, 'l' => 'Ank. Spondylitis', 'k' => 'ankylosing_spondylitis', 'c' => '#bb6633', 'w' => 85],
                ['x' => 440, 'l' => 'Multiple Sclerosis', 'k' => 'ms', 'c' => '#5577cc', 'w' => 100],
                ['x' => 560, 'l' => 'Type 1 Diabetes', 'k' => 'diabetes1', 'c' => '#4499aa', 'w' => 90],
                ['x' => 680, 'l' => 'Vasculitis', 'k' => 'vasculitis', 'c' => '#cc5555', 'w' => 70],
                ['x' => 780, 'l' => 'Pemphigus', 'k' => 'pemphigus', 'c' => '#cc6688', 'w' => 75],
                ['x' => 880, 'l' => 'GBS / CIDP', 'k' => 'gbs', 'c' => '#6688bb', 'w' => 75],
                ['x' => 990, 'l' => 'CLE / SCLE', 'k' => 'cle', 'c' => '#cc5588', 'w' => 75],
                ['x' => 1080, 'l' => 'ALS', 'k' => 'als', 'c' => '#778899', 'w' => 40],
            ],
        ];
    }

    return [
        'row1' => [
            ['x' => 150, 'l' => 'Rheumatoide Arthritis', 'k' => 'ra', 'c' => '#cc6622', 'w' => 130],
            ['x' => 370, 'l' => 'Psoriasis (klass. + paradox)', 'k' => 'psoriasis', 'c' => '#dd5500', 'w' => 160],
            ['x' => 580, 'l' => 'Atopische Dermatitis', 'k' => 'ad', 'c' => '#44cc77', 'w' => 120],
            ['x' => 760, 'l' => 'Sjögren-Syndrom', 'k' => 'sjogren', 'c' => '#6644aa', 'w' => 110],
            ['x' => 930, 'l' => 'SLE / Lupus', 'k' => 'sle', 'c' => '#cc4488', 'w' => 85],
            ['x' => 1050, 'l' => 'Urtikaria', 'k' => 'urticaria', 'c' => '#dd6699', 'w' => 70],
        ],
        'row2' => [
            ['x' => 100, 'l' => 'M. Crohn', 'k' => 'crohn', 'c' => '#cc7744', 'w' => 70],
            ['x' => 200, 'l' => 'Colitis ulcerosa', 'k' => 'cu', 'c' => '#cc8855', 'w' => 95],
            ['x' => 320, 'l' => 'M. Bechterew', 'k' => 'ankylosing_spondylitis', 'c' => '#bb6633', 'w' => 85],
            ['x' => 440, 'l' => 'Multiple Sklerose', 'k' => 'ms', 'c' => '#5577cc', 'w' => 100],
            ['x' => 560, 'l' => 'Diabetes Typ 1', 'k' => 'diabetes1', 'c' => '#4499aa', 'w' => 90],
            ['x' => 680, 'l' => 'Vaskulitis', 'k' => 'vasculitis', 'c' => '#cc5555', 'w' => 70],
            ['x' => 780, 'l' => 'Pemphigus', 'k' => 'pemphigus', 'c' => '#cc6688', 'w' => 75],
            ['x' => 880, 'l' => 'GBS / CIDP', 'k' => 'gbs', 'c' => '#6688bb', 'w' => 75],
            ['x' => 990, 'l' => 'CLE / SCLE', 'k' => 'cle', 'c' => '#cc5588', 'w' => 75],
            ['x' => 1080, 'l' => 'ALS', 'k' => 'als', 'c' => '#778899', 'w' => 40],
        ],
    ];
}

/**
 * Disease list order for the diseases tab.
 */
function get_disease_keys(): array
{
    return [
        'psoriasis', 'ra', 'sjogren', 'ad', 'urticaria', 'crohn', 'ankylosing_spondylitis',
        'sle', 'cle', 'cu', 'ms', 'gbs', 'cidp', 'polymyositis', 'diabetes1',
        'pemphigus', 'vasculitis', 'als',
    ];
}
