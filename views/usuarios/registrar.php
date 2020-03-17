<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Registrar un usuario');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuarios-registrar">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('app', 'Introduzca los siguientes datos para registrarse:') ?></p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => ['wrapper' => 'col-sm-5'],
        ],
    ]); ?>

        <?= $form->field($model, 'login')->textInput(['autofocus' => true]) ?>
        <?= $form->field($model, 'nombre')->textInput() ?>
        <?= $form->field($model, 'apellidos')->textInput() ?>
        <?= $form->field($model, 'email')->textInput() ?>
        <?= $form->field($model, 'fnac')->textInput() ?>
        <?= $form->field($model, 'rol')->textInput() ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'password_repeat')->passwordInput()->label(Yii::t('app', 'Password Repeat')) ?>

        <div class="form-group">
            <div class="offset-sm-2">
                <?= Html::submitButton('Registrarse', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

</div>