<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Canciones */
/* @var $form yii\bootstrap4\ActiveForm */

// $cookieValue = isset($_COOKIE['album']) && $_COOKIE['album'] == 'true' ? $_COOKIE['album'] : 'false';
// setcookie('album', $cookieValue, time() + 3600 * 24 * 30, '/');

$js = <<<EOT
    const cookie = getCookie('album');
    if ($('.is-album-check').prop('checked')) {
        $('.field-canciones-album_id').show();
        $('#canciones-album_id').attr('name', 'Canciones[album_id]');
        $('#canciones-album_id').prev().attr('name', 'Canciones[album_id]');
        $('#canciones-portada').removeAttr('name');
        $('#canciones-portada').prev().removeAttr('name');
        $('.field-canciones-portada').hide();
    } else {
        $('.field-canciones-album_id').hide();
        $('#canciones-album_id').removeAttr('name');
        $('#canciones-album_id').prev().removeAttr('name');
        $('#canciones-portada').attr('name', 'Canciones[portada]');
        $('#canciones-portada').prev().attr('name', 'Canciones[portada]');
        $('.field-canciones-portada').show();
    }
EOT;

$this->registerJS($js);

?>

<div class="canciones-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'create-form']]); ?>

    <?= $form->field($model, 'titulo')->textInput(['maxlength' => true]) ?>

    <div class="custom-control custom-switch mb-3">
        <input type="checkbox" class="custom-control-input is-album-check" id="customSwitch1" <?= isset($_COOKIE['album']) && $_COOKIE['album'] == 'true' ? 'checked' : '' ?>>
        <label class="custom-control-label" for="customSwitch1"><?= Yii::t('app', 'BelongAlbum') ?></label>
    </div>

    <?= $form->field($model, 'album_id')->dropDownList($albumes)->label(Yii::t('app', 'Album')) ?>

    <?= $form->field($model, 'genero_id')->dropDownList($generos)->label(Yii::t('app', 'Género')) ?>

    <?= $form->field($model, 'portada')->fileInput() ?>

    <?= $form->field($model, 'explicit')->checkbox() ?>

    <?= $form->field($model, 'cancion')->fileInput(['class' => 'song-file-input']) ?>

    <?= $form->field($model, 'anyo')->textInput() ?>

    <?= $form->field($model, 'duracion')->textInput(['disabled' => true, 'name' => '']) ?>
    <?= Html::activeHiddenInput($model, 'duracion', ['id' => 'duration-hidden']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn main-yellow send-btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
