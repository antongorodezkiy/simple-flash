<?php

use Tamtamchik\SimpleFlash\TemplateFactory;
use Tamtamchik\SimpleFlash\Templates;

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

flash()->error(array('Invalid email!', 'Invalid username!'))
    ->warning('Warning message.')
    ->info('Info message.')
    ->success('Success message!');

flash()->setTemplate(TemplateFactory::create(Templates::UIKIT_3));

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test UiKit 3 template example.</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.12/css/uikit.min.css">
</head>
<body>

<a href="https://github.com/tamtamchik/simple-flash"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/365986a132ccd6a44c23a9169022c0b5c890c387/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f7265645f6161303030302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_red_aa0000.png"></a>
<br/>

<div class="uk-container">

    <?php include_once '_menu.php'; ?>

    <hr/>

    <?= flash() ?>

</div>

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.12/js/uikit.min.js"></script>

</body>
</html>
