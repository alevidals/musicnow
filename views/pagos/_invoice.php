<?php

use yii\bootstrap4\Html;

?>

<div>
    <img src="https://firebasestorage.googleapis.com/v0/b/jiejie-test.appspot.com/o/images%2Fmusic_now_letras.png?alt=media&token=543c7b8c-28b3-4f99-a1f9-96c0dde95b72" alt="logo">
    <h4><?= Yii::$app->formatter->asDate(new DateTime()) ?></h4>
    <hr>
    <div>
        <p><?= $pago->nombre ?></p>
        <p><?= $pago->apellidos ?></p>
        <p><?= $pago->direccion ?>, <?= $pago->getProvincia()->one()->denominacion ?></p>
        <p><?= $_SERVER['SERVER_NAME'] ?></p>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th><?= Yii::t('app', 'Quantity') ?></th>
                <th><?= Yii::t('app', 'Concept') ?></th>
                <th><?= Yii::t('app', 'Price') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Cuenta premium</td>
                <td>10 €</td>
            </tr>
        </tbody>
    </table>
    <hr>
    <h3 class="text-right">Total: 10 €</h3>
</div>