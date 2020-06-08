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
    <img src="https://firebasestorage.googleapis.com/v0/b/jiejie-test.appspot.com/o/default%2Fmusic_now_letras.png?alt=media&token=2df69831-c061-4903-9266-7464f8d7c140" width="500px" alt="mail-image">
    <h1><?= Yii::t('app', 'ToRecover') ?></h1>
    <a href="<?= $content ?>" role="button" style="display: inline-block; font-weight: 400; color: #212529; text-align: center; vertical-align: middle; cursor: pointer; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; background-color: transparent; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; line-height: 1.5; border-radius: 0.25rem; transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; background-color: #ffba00; text-decoration: none;"><?= Yii::t('app', 'Verify') ?></a>
</body>
</html>