<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AlbumesCanciones */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="albumes-canciones-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'album_id')->textInput() ?>

    <?= $form->field($model, 'canciones_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn main-yellow']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
