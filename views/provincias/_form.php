<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Provincias */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="provincias-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'denominacion')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
