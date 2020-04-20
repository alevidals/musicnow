<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CancionesSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="canciones-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'titulo') ?>

    <?= $form->field($model, 'album_id') ?>

    <?= $form->field($model, 'genero_id') ?>

    <?= $form->field($model, 'url_cancion') ?>

    <?php // echo $form->field($model, 'image_name') ?>

    <?php // echo $form->field($model, 'usuario_id') ?>

    <?php // echo $form->field($model, 'url_portada') ?>

    <?php // echo $form->field($model, 'anyo') ?>

    <?php // echo $form->field($model, 'duracion') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
