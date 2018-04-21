<?php

require __DIR__ . '/config.php';

$jigsaws = json_decode(file_get_contents(FILE));

$offset = null;

foreach ($jigsaws->jigsaws as $k => $jigsaw) {
    if ($jigsaw->id === ($_GET['id'] ?? null)) {
        $offset = $k;
    }
}

if ($offset === null) {
    return;
}

$raw = json_decode(file_get_contents('php://input'));

$sanitized = [];

foreach ($raw->pieces ?? [] as $piece) {
    $sanitized[] = (object) [
        'left'  => (int) $piece->left,
        'right' => (int) $piece->right,
        'text'  => $piece->text
    ];
}

$jigsaws->jigsaws[$offset]->pieces = $sanitized;

file_put_contents(FILE, json_encode($jigsaws));
