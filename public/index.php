<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Niccolo\DocparserPhp\Controller\ValidationController;

$controller = new ValidationController();
$view = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $view = $controller->handleRequest(data: $_POST);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>HTML Validator</title>
</head>

<body>

    <h1>HTML Validator</h1>

    <form method="post" action="">
        <label for="context">Insert content:</label><br>
        <textarea name="context" id="context"><?= htmlspecialchars(string: $_POST['context'] ?? '') ?></textarea><br><br>

        <label for="type">Data type:</label>
        <select name="type" id="type">
            <option value="html" <?= (($_POST['type'] ?? '') === 'html') ? 'selected' : '' ?>>HTML</option>
        </select><br><br>

        <button type="submit">Validate</button>
    </form>

    <?php if ($view): ?>
        <div>
            <?= $view->render(); ?>
        </div>
    <?php endif; ?>

</body>

</html>