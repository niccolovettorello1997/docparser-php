<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Niccolo\DocparserPhp\Controller\ParserController;

$controller = new ParserController();
$views = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $views = $controller->handleRequest(data: $_POST);

    if ($_POST['renderingType'] === 'json') {
        $jsonResult = $controller->getJsonResult(views: $views);

        header('Content-Type: application/json; charset=utf-8');

        if ($jsonResult->getStatusCode() === 200) {
            header('Content-Disposition: attachment; filename="parsed.json"');
        }

        http_response_code($jsonResult->getStatusCode());
        echo $jsonResult->getContent();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Docparser-PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>Docparser-PHP</h1>
        <p class="subtitle">Upload a file or paste code and choose how to get the result.</p>

        <form method="post" action="" enctype="multipart/form-data" class="parser-form">
            <label for="context">Content</label>
            <textarea name="context" id="context" placeholder="Paste here your code..."><?= htmlspecialchars(string: $_POST['context'] ?? '') ?></textarea>

            <label for="file">Upload a file</label>
            <input type="file" name="file" id="file" />

            <label for="type">Language type</label>
            <select name="type" id="type">
                <option value="html" <?= (($_POST['type'] ?? '') === 'html') ? 'selected' : '' ?>>HTML</option>
                <option value="markdown" <?= (($_POST['type'] ?? '') === 'markdown') ? 'selected' : '' ?>>Markdown</option>
            </select>

            <div class="form-actions">
                <button type="submit" name="renderingType" value="html" class="btn">Show result as HTML</button>
                <button type="submit" name="renderingType" value="json" class="btn secondary">Download JSON</button>
            </div>
        </form>

        <div class="results">
        <?php if (!empty($views)): ?>
            <?php foreach ($views as $view): ?>
                <?php
                $rendered = $view->render();
                $class = 'box-generic';

                if (stripos(haystack: $rendered, needle: 'error') !== false) {
                    $class = 'box-error';
                } elseif (stripos(haystack: $rendered, needle: 'warning') !== false) {
                    $class = 'box-warning';
                } elseif (stripos(haystack: $rendered, needle: 'valid') !== false) {
                    $class = 'box-success';
                }
                ?>
                <div class="box <?= $class ?>">
                    <?= $rendered ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    </div>

</body>
</html>
