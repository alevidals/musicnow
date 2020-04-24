<?php

use app\models\Generos;
use app\models\Usuarios;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CancionesSearch */
/* @var $form yii\bootstrap4\ActiveForm */

$generos = ['' => ''] + Generos::lista();
$albumes = ['' => ''] + Usuarios::findOne(Yii::$app->user->id)->getAlbumes()->select('titulo')->indexBy('id')->column();

?>

<div class="canciones-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <button class="btn btn-primary filter-btn" type="button"><?= Yii::t('app', 'ShowFilters') ?></button>

    <div class="filters mt-4">
        <div class="row">
            <?php if (Yii::$app->user->identity->rol_id == 1) : ?>
                <div class="col-lg col-12">
                    <?= $form->field($model, 'usuario.login') ?>
                </div>
            <?php endif; ?>
            <div class="col-lg col-12">
                <?= $form->field($model, 'titulo') ?>
            </div>
            <div class="col-lg col-12">
                <?= $form->field($model, 'album.titulo')->label(Yii::t('app', 'Albumes')) ?>
            </div>
            <div class="col-lg col-12">
                <?= $form->field($model, 'genero_id')->label(Yii::t('app', 'Generos'))->dropDownList($generos) ?>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-secondary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>


</div>
