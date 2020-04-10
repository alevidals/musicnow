<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Canciones */
/* @var $form yii\bootstrap4\ActiveForm */

?>

<div class="canciones-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'titulo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'album_id')->dropDownList($albumes)->label(Yii::t('app', 'Album')) ?>

    <?= $form->field($model, 'genero_id')->dropDownList($generos)->label(Yii::t('app', 'Género')) ?>

    <?= $form->field($model, 'portada')->fileInput() ?>

    <?= $form->field($model, 'cancion')->fileInput() ?>

    <?= $form->field($model, 'anyo')->textInput() ?>

    <?= $form->field($model, 'duracion')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success send-btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
