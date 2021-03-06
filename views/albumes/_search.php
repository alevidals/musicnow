<?php

use kartik\datecontrol\DateControl;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AlbumesSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="albumes-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => ['inputOptions' => ['autocomplete' => 'off']]
    ]); ?>

    <button class="btn btn-primary filter-btn" type="button"><?= Yii::t('app', 'ShowFilters') ?></button>

    <div class="filters mt-4">
        <div class="row">
            <?php if (Yii::$app->user->identity->rol_id == 1) : ?>
                <div class="col-lg-3 col-12">
                    <?= $form->field($model, 'usuario.login') ?>
                </div>
            <?php endif; ?>
            <div class="col-lg-3 col-12">
                <?= $form->field($model, 'titulo') ?>
            </div>
            <div class="col-lg-3 col-12">
                <?= $form->field($model, 'anyo') ?>
            </div>
            <div class="col-lg-3 col-12">
                <?= $form->field($model, 'created_at')->widget(
                    DateControl::classname(),
                    [
                    'type' => DateControl::FORMAT_DATE,
                    'displayFormat' => 'php:d-m-Y',
                    ]
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
