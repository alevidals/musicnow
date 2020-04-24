<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UsuariosSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="usuarios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <button class="btn btn-primary filter-btn" type="button"><?= Yii::t('app', 'ShowFilters') ?></button>

    <div class="filters mt-4">
        <div class="row">
            <div class="col-lg col-12">
                <?= $form->field($model, 'login') ?>
            </div>
            <div class="col-lg col-12">
                <?= $form->field($model, 'nombre') ?>
            </div>
            <div class="col-lg col-12">
                <?= $form->field($model, 'apellidos') ?>
            </div>
            <div class="col-lg col-12">
                <?= $form->field($model, 'email') ?>
            </div>
            <div class="col-lg col-12">
                <?= $form->field($model, 'rol.rol') ?>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>

    <?php // echo $form->field($model, 'url_image')?>

    <?php // echo $form->field($model, 'image_name')?>

    <?php // echo $form->field($model, 'password') ?>

    <?php // echo $form->field($model, 'fnac') ?>

    <?php // echo $form->field($model, 'rol_id') ?>

    <?php // echo $form->field($model, 'auth_key') ?>

    <?php // echo $form->field($model, 'confirm_token') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'deleted_at') ?>

    <?php ActiveForm::end(); ?>

</div>
