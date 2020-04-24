<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\RegistrarForm */

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

$this->title = 'Registrar usuario';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Introduzca los siguientes datos para registrarse:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'register-form',
        'layout' => 'horizontal',
        'action' => ['usuarios/registrar'],
        'fieldConfig' => [
            'horizontalCssClasses' => ['wrapper' => 'col-lg-12 mx-auto'],
        ],
    ]); ?>
            <h2 class="fs-title"><?= Yii::t('app', 'Regístrate con tu dirección de correo electrónico') ?></h2>
            <?= $form->field($model, 'nombre')->textInput()->label(Yii::t('app', 'Nombre') . '*', ['class' => 'col-12']) ?>
            <?= $form->field($model, 'apellidos')->textInput()->label(Yii::t('app', 'Apellidos') . '*', ['class' => 'col-12']) ?>
            <?= $form->field($model, 'login')->textInput(['autofocus' => true])->label(Yii::t('app', 'Nombre de usuario') . '*', ['class' => 'col-12']) ?>
            <?= Html::button(Yii::t('app', 'Siguiente'), ['type' => 'button', 'name' => 'next', 'class' => 'next action-button btn main-yellow']) ?>
            <h2 class="fs-title"><?= Yii::t('app', 'Regístrate con tu dirección de correo electrónico') ?></h2>
            <div class="row">
                <div class="col-md-6 col-12">
                    <?= $form->field($model, 'password')->passwordInput()->label(Yii::t('app', 'Password') . '*', ['class' => 'col-12']) ?>
                </div>
                <div class="col-md-6 col-12">
                    <?= $form->field($model, 'password_repeat')->passwordInput()->label(Yii::t('app', 'Password Repeat') . '*', ['class' => 'col-12']) ?>
                </div>
            </div>
            <?= $form->field($model, 'email')->textInput()->label('Email*', ['class' => 'col-12']) ?>
            <?= $form->field($model, 'fnac')->textInput()->label(Yii::t('app', 'Fnac'), ['class' => 'col-12']) ?>
            <?= $form->field($model, 'rol_id')->textInput()->label('Rol', ['class' => 'col-12']) ?>
            <?= Html::button(Yii::t('app', 'Anterior'), ['type' => 'button', 'name' => 'previous', 'class' => 'previous action-button-previous btn main-yellow']) ?>
            <?= Html::submitButton(Yii::t('app', 'Registrarse'), ['class' => ' btn main-yellow rounded', 'name' => 'register-button']) ?>
    <?php ActiveForm::end(); ?>
</div>
