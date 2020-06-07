<?php

use yii\bootstrap4\Html;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <img src="https://firebasestorage.googleapis.com/v0/b/jiejie-test.appspot.com/o/music_now_naranja_largo.png?alt=media&token=d6d99f07-d179-4ee9-88a3-48b2e8bbe9fd" width="500px" alt="mail-image">
    <h1><?= Yii::t('app', 'click-to-reset') ?></h1>
    <a href="<?= $content ?>" role="button" style="display: inline-block; font-weight: 400; color: #212529; text-align: center; vertical-align: middle; cursor: pointer; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; background-color: transparent; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; line-height: 1.5; border-radius: 0.25rem; transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; background-color: #ffba00; text-decoration: none;"><?= Yii::t('app', 'Verify') ?></a>
</body>
</html>