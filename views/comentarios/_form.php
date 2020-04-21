<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Comentarios */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="comentarios-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'create-form']]); ?>
    <?= $form->field($model, 'usuario_id')->textInput() ?>

    <?= $form->field($model, 'cancion_id')->textInput() ?>

    <?= $form->field($model, 'comentario')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
