<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PlaylistsSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="playlists-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <button class="btn btn-primary filter-btn" type="button"><?= Yii::t('app', 'ShowFilters') ?></button>

    <div class="filters mt-4">
        <div class="row">
            <?php if (Yii::$app->user->identity->rol_id == 1) : ?>
                <div class="col-6">
                    <?= $form->field($model, 'usuario.login') ?>
                </div>
            <?php endif; ?>
            <div class="col">
                <?= $form->field($model, 'titulo') ?>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
