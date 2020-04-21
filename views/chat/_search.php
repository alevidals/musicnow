<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ChatSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="chat-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <button class="btn btn-primary filter-btn" type="button"><?= Yii::t('app', 'ShowFilters') ?></button>

    <div class="filters mt-4">
        <div class="row">
            <div class="col-lg col-12">
                <?= $form->field($model, 'emisor.login')->label(Yii::t('app', 'Emisor')) ?>
            </div>
            <div class="col-lg col-12">
                <?= $form->field($model, 'receptor.login')->label(Yii::t('app', 'Receptor')) ?>
            </div>
            <div class="col-lg col-12">
                <?= $form->field($model, 'mensaje')->label(Yii::t('app', 'Mensaje')) ?>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
