<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LikesSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="likes-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <button class="btn btn-primary filter-btn" type="button"><?= Yii::t('app', 'ShowFilters') ?></button>

    <div class="filters mt-4">
        <div class="row">
            <div class="col-6">
                <?= $form->field($model, 'usuario.login') ?>
            </div>
            <div class="col-6">
                <?= $form->field($model, 'cancion.titulo')->label(Yii::t('app', 'Song')) ?>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
