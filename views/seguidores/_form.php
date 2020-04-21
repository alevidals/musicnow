<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Seguidores */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="seguidores-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'create-form']]); ?>
    <?= $form->field($model, 'seguidor_id')->textInput() ?>

    <?= $form->field($model, 'seguido_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
