<?php require __DIR__ . '/config.php' ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Jigsaw searcher</title>
    </head>
    <body>
        <h1>Jigsaw searcher</h1>
        <a href="new.php"><button>Create new jigsaw</button></a>
        <p>These jigsaws have already been made available by you and other users:</p>
        <ul>
            <li>vowel</li>
            <li>consonant</li>
            <li>letter</li>
            <li>non-letter</li>
            <?php foreach (json_decode(file_get_contents(FILE))->jigsaws as $jigsaw): ?>
                <li><a target="_blank" href="edit.php?id=<?= $jigsaw->id ?>"><?= $jigsaw->name ?></a></li>
            <?php endforeach ?>
        </ul>
    </body>
</html>
