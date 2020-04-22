<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BloqueadosSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="bloqueados-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <button class="btn btn-primary filter-btn" type="button"><?= Yii::t('app', 'ShowFilters') ?></button>

    <div class="filters mt-4">
        <div class="row">
            <div class="col 6">
                <?= $form->field($model, 'bloqueador.login')->label(Yii::t('app', 'Bloqueador')) ?>
            </div>
            <div class="col 6">
                <?= $form->field($model, 'bloqueado.login')->label(Yii::t('app', 'Bloqueado')) ?>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
