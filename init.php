<?php

require __DIR__ . '/config.php';

if (!file_exists(FILE)) {
    file_put_contents(FILE, '{"jigsaws":[]}');
}
