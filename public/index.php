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

        header(header: 'Content-Type: application/json; charset=utf-8');

        if ($jsonResult->getStatusCode() === 200) {
            header(header: 'Content-Disposition: attachment; filename="parsed.json"');
        }

        http_response_code(response_code: $jsonResult->getStatusCode());
        echo $jsonResult->getContent();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>HTML Parser</title>
</head>

<body>

    <h1>HTML Parser</h1>

    <form method="post" action="" enctype="multipart/form-data">
        <label for="context">Insert content:</label><br>
        <textarea name="context" id="context"><?= htmlspecialchars(string: $_POST['context'] ?? '') ?></textarea><br><br>

        <input type="file" name="file" /><br><br>

        <label for="type">Data type:</label>
        <select name="type" id="type">
            <option value="html" <?= (($_POST['type'] ?? '') === 'html') ? 'selected' : '' ?>>HTML</option>
            <option value="markdown" <?= (($_POST['type'] ?? '') === 'markdown') ? 'selected' : '' ?>>Markdown</option>
        </select><br><br>

        <button type="submit" name="renderingType" value="html">Parse and see result in browser</button>
        <button type="submit" name="renderingType" value="json">Parse and download JSON</button>
    </form>

    <?php if (!empty($views)): ?>
        <?php foreach ($views as $view): ?>
        <div>
            ++++++++++++++++++++++++++++++
        </div>
        <div>
            <?= $view->render(); ?>
        </div>
        <div>
            ++++++++++++++++++++++++++++++
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>

</html>