<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$lang = $_GET['lang'] ?? 'en';
if (!isset(LANGUAGES[$lang])) {
    $lang = 'en';
}

$tab = $_GET['tab'] ?? 'diagram';
$sel = $_GET['sel'] ?? null;
$dark = ($_COOKIE['theme'] ?? 'dark') === 'dark';

$topics = load_all_topics($lang);
$info = $sel && isset($topics[$sel]) ? $topics[$sel] : null;

if ($tab === 'detail' && !$info) {
    $tab = 'diagram';
}

$theme = $dark ? THEME_DARK : THEME_LIGHT;
$columns = get_columns();
$rows = get_rows();

include __DIR__ . '/templates/layout.php';
