<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Likes */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="likes-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'create-form']]); ?>
    <?= $form->field($model, 'usuario_id')->textInput() ?>

    <?= $form->field($model, 'cancion_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn main-yellow']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
