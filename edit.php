<?php

require __DIR__ . '/config.php';

$jigsaws = json_decode(file_get_contents(FILE));

$jigsaw = null;

foreach ($jigsaws->jigsaws as $j) {
    if ($j->id === ($_GET['id'] ?? null)) {
        $jigsaw = $j;
    }
}

if ($jigsaw === null) {
    return;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script><script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="/style.css">
<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="jigsaw.js"></script>
<script>window.jigsaw = {id: '<?= $jigsaw->id ?>'};</script>
</head>
<body><div class="padded">
    <h1>Jigsaw: <?= $jigsaw->name ?></h1>
        <div class="jigsaw-container">
<div class="jigsaw">
    <?php foreach ($jigsaw->pieces as $piece): ?>
        <div class="jigsaw-piece">
            <img data-side="left" data-edge="<?= $piece->left ?>" src="images/left/<?= $piece->left ?>.png" height="32">
            <span class="jigsaw-text"><?= htmlspecialchars($piece->text) ?></span>
            <img data-side="right" data-edge="<?= $piece->right ?>" src="images/right/<?= $piece->right ?>.png" height="32">
        </div>
    <?php endforeach ?>
</div>
<div class="jigsaw-toolbar">
<input size="5"><div class="separator"></div>
<button data-side="left" data-edge="0"><img src="images/left/0.png" height="18"></button><button data-side="left" data-edge="1"><img src="images/left/1.png" height="18"></button><button data-side="left" data-edge="2"><img src="images/left/2.png" height="18"></button><button data-side="left" data-edge="3"><img src="images/left/3.png" height="18"></button><button data-side="left" data-edge="4"><img src="images/left/4.png" height="18"></button><div class="separator"></div>
<button data-side="right" data-edge="0"><img src="images/right/0.png" height="18"></button><button data-side="right" data-edge="1"><img src="images/right/1.png" height="18"></button><button data-side="right" data-edge="2"><img src="images/right/2.png" height="18"></button><button data-side="right" data-edge="3"><img src="images/right/3.png" height="18"></button><button data-side="right" data-edge="4"><img src="images/right/4.png" height="18"></button><div class="separator"></div>
<a href="#" class="add"><img src="images/add.png" height="24"></a><div class="separator"></div>
<a href="#" class="remove"><img src="images/remove.png" height="20"></a>
</div>
<textarea class="jigsaw-textarea" readonly></textarea>
</div>

<button id="save">Search a file</button>

</div></body>
</html>
