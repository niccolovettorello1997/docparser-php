<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Niccolo\DocparserPhp\Controller\ParserController;
use Niccolo\DocparserPhp\Service\ValidatorService;
use Niccolo\DocparserPhp\Service\ParserService;

$validatorService = new ValidatorService();
$parserService = new ParserService();

$controller = new ParserController(
    validatorService: $validatorService,
    parserService: $parserService,
);

$view = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $view = $controller->handleRequest(data: $_POST);

    if ($_POST['renderingType'] === 'json') {
        header(header: 'Content-Type: application/json; charset=utf-8');
        header(header: 'Content-Disposition: attachment; filename="parsed.json"');
        
        http_response_code(response_code: 200);
        
        echo $view->render();
        
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
        <?php if (null !== $view): ?>
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
        <?php endif; ?>
    </div>
    </div>

</body>
</html>
