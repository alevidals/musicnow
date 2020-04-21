<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Estados */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="estados-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'create-form']]); ?>
    <?= $form->field($model, 'estado')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
