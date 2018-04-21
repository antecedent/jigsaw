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

$builtIn =  [
    'vowel' => '[aeiouyąęėįųū]',
    'consonant' => '[bcčdfgjklmnprsštvzž]',
    'letter' => '[aeiouyąęėįųūbcčdfgjklmnprsštvzž]',
    'non-letter' => '[^aeiouyąęėįųūbcčdfgjklmnprsštvzž]'
];

function jigsawExists($name)
{
    global $jigsaws;
    foreach ($jigsaws->jigsaws as $j) {
        if ($j->name === $name) {
            return true;
        }
    }
    return false;
}

function getJigsaw($name)
{
    global $jigsaws;
    foreach ($jigsaws->jigsaws as $j) {
        if ($j->name === $name) {
            return $j;
        }
    }
    return null;
}

function jigsawToRegex($jigsaw)
{
    global $builtIn;
    $limit = 6;
    $regexes = [];
    # Initialize
    for ($k = 0; $k <= $limit; $k++) {
        $regexes[$k] = [];
        for ($i = 0; $i < $limit; $i++) {
            $regexes[$k][$i] = [];
            for ($j = 0; $j < $limit; $j++) {
                $regexes[$k][$i][$j] = null;
                if ($k === 0) {
                    $union = [];
                    foreach ($jigsaw->pieces as $piece) {
                        if ($piece->left === $i && ($piece->right ?: ($limit - 1)) === $j) {
                            if ($piece->text[0] === '[' && isset($builtIn[substr($piece->text, 1, -1)])) {
                                $union[] = $builtIn[substr($piece->text, 1, -1)];
                            } elseif ($piece->text[0] === '[' && jigsawExists(substr($piece->text, 1, -1))) {
                                $union[] = jigsawToRegex(getJigsaw(substr($piece->text, 1, -1)));
                            } else {
                                $union[] = preg_quote($piece->text);
                            }
                        }
                    }
                    $regexes[$k][$i][$j] = $union ? join('|', $union) : (($i === $j) ? '' : null);
                }
            }
        }
    }
    # Compute the transitive closure
    for ($k = 1; $k <= $limit; $k++) {
        for ($i = 0; $i < $limit; $i++) {
            for ($j = 0; $j < $limit; $j++) {
                $union = [];
                if ($regexes[$k - 1][$i][$j] !== null) {
                    $union[] = $regexes[$k - 1][$i][$j];
                }
                $concatenation = [
                    $regexes[$k - 1][$i][$k - 1],
                    ($regexes[$k - 1][$k - 1][$k - 1] === null) ? null : ('(' . $regexes[$k - 1][$k - 1][$k - 1] . ')*'),
                    $regexes[$k - 1][$k - 1][$j],
                ];
                if (!empty($concatenation) && !in_array(null, $concatenation, true)) {
                    $union[] = join('', $concatenation);
                }
                if (!empty($union)) {
                    $regexes[$k][$i][$j] = '(' . join('|', array_unique($union)) . ')';
                }
            }
        }
    }

    return '(' . $regexes[$limit][0][$limit - 1] . ')';
}


if (isset($_FILES['input'])) {

    $text = file_get_contents($_FILES['input']['tmp_name']);


    preg_match_all('/[\s.,;-?!]' . jigsawToRegex($jigsaw) . '[\s.,;-?!]/ui', $text, $matches, PREG_OFFSET_CAPTURE);


    $results = [];

    $margin = 50;

    foreach ($matches[0] as $match) {
        $results[] = str_replace($match[0], '<strong style="color: red;">' . $match[0] . '</strong>', substr($text, max(0, $match[1] - $margin), $margin * 2 + strlen($match[0])));
    }

}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Jigsaw searcher</title>
        <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script><script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
        <script>
            $(function() {
                $('#file').change(function() {
                    $('form').submit();
                });
            });
        </script>
    </head>
    <body>
        <h1>Search using: <?= $jigsaw->name ?></h1>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" id="file" name="input">
        </form>
        <?php if (isset($results)): ?>
            <h2>Results</h2>
            <?php if (empty($results)): ?>
                No matches.
            <?php else: ?>
                <?php foreach ($results as $result): ?>
                    <pre><?= $result ?></pre>
                <?php endforeach ?>
            <?php endif ?>
        <?php endif ?>
    </body>
</html>
