<?php

require __DIR__ . '/config.php';

if (!isset($_POST['name']) || $_POST['name'] === '') {
    header('Location: new.php');
    return;
}

$jigsaws = json_decode(file_get_contents(FILE));

$id = uniqid();

$jigsaws->jigsaws[] = [
    'id' => $id,
    'name' => $_POST['name'],
    'pieces' => new stdClass
];

file_put_contents(FILE, json_encode($jigsaws));

header('Location: edit.php?id=' . $id);
