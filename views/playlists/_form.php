<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Playlists */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="playlists-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'create-form']]); ?>
    <?= $form->field($model, 'titulo')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn main-yellow']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
