<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ComentariosSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="comentarios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <button class="btn btn-primary filter-btn" type="button"><?= Yii::t('app', 'ShowFilters') ?></button>

    <div class="filters mt-4">
        <div class="row">
            <?php if (Yii::$app->user->identity->rol_id == 1) : ?>
                <div class="col-lg-3 col-12">
                    <?= $form->field($model, 'usuario.login') ?>
                </div>
            <?php endif; ?>
            <div class="col-lg col-12">
                <?= $form->field($model, 'cancion.titulo')->label(Yii::t('app', 'Song')) ?>
            </div>
            <div class="col-lg col-12">
                <?= $form->field($model, 'comentario')->label(Yii::t('app', 'Comment')) ?>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
