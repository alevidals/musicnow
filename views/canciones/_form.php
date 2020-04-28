<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Canciones */
/* @var $form yii\bootstrap4\ActiveForm */

?>

<div class="canciones-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'create-form']]); ?>

    <?= $form->field($model, 'titulo')->textInput(['maxlength' => true]) ?>

    <div class="custom-control custom-switch mb-3">
        <input type="checkbox" class="custom-control-input is-album-check" id="customSwitch1" checked>
        <label class="custom-control-label" for="customSwitch1"><?= Yii::t('app', 'BelongAlbum') ?></label>
    </div>

    <?= $form->field($model, 'album_id')->dropDownList($albumes)->label(Yii::t('app', 'Album')) ?>

    <?= $form->field($model, 'genero_id')->dropDownList($generos)->label(Yii::t('app', 'Género')) ?>

    <?= $form->field($model, 'portada')->fileInput(['name' => '']) ?>

    <?= $form->field($model, 'explicit')->checkbox() ?>

    <?= $form->field($model, 'cancion')->fileInput(['class' => 'song-file-input']) ?>

    <?= $form->field($model, 'anyo')->textInput() ?>

    <?= $form->field($model, 'duracion')->textInput(['disabled' => true, 'name' => '']) ?>
    <?= Html::activeHiddenInput($model, 'duracion', ['id' => 'duration-hidden']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success send-btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
