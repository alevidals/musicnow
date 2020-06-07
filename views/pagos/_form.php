<?php

use app\models\Usuarios;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Pagos */
/* @var $form yii\bootstrap4\ActiveForm */

?>

<div class="pagos-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'apellidos')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'provincia_id')->dropDownList($provincias)->label(Yii::t('app', 'ProvCities')) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'direccion')->textInput(['maxlength' => true]) ?>
        </div>
        <?php if ($regalo !== null) : ?>
            <div class="col-lg-6">
                <?= $form->field($model, 'receptor_id')->dropDownList(Usuarios::lista())->label(Yii::t('app', 'Friend')) ?>
            </div>
        <?php endif ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Pay'), ['class' => 'btn btn-success mx-auto d-block my-5']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
