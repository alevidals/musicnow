<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\models\Usuarios;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$usuario = new Usuarios(['scenario' => Usuarios::SCENARIO_CREAR]);

$this->title = 'Login';
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <!-- <p>Rellena los siguientes campos:</p> -->

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => ['wrapper' => 'col-sm-5'],
        ],
    ]); ?>

<ul class="nav nav-tabs justify-content-center nav-fill mb-5" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="true">LOGIN</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="false">REGISTER</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
            <div class="row">
                <div class="col-lg-6">
                    <div class="mx-auto mb-5">
                        <?= Html::img('@web/img/music_now_letras.png', ['alt' => Yii::$app->name, 'class' => 'logo ml-auto', 'width' => '300px']) ?>
                    </div>

                    <div class="row">
                        <div class="mx-auto col-lg-12">
                            <?php $form = ActiveForm::begin([
                                'id' => 'login-form',
                                'layout' => 'horizontal',
                                'fieldConfig' => [
                                    'horizontalCssClasses' => ['wrapper' => 'col-lg-12 mx-auto'],
                                ],
                            ]); ?>

                                <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Username'])->label(false) ?>

                                <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password'])->label(false) ?>

                                <?= $form->field($model, 'rememberMe')->checkbox() ?>

                                <div class="form-group">
                                    <?= Html::submitButton('Login', ['class' => 'mx-auto btn btn-warning btn-block rounded', 'name' => 'login-button']) ?>
                                </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <?= Html::img('@web/img/undraw_listening_1u79.svg', ['alt' => 'girl-music', 'class' => 'img-fluid']) ?>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
            <div class="row">
                <div class="col-lg-6 order-2 order-lg-1 align-self-center">
                    <?= Html::img('@web/img/undraw_compose_music_ovo2.svg', ['alt' => 'girl-music', 'class' => 'img-fluid']) ?>
                </div>
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="mx-auto mb-5">
                        <?= Html::img('@web/img/music_now_letras.png', ['alt' => Yii::$app->name, 'class' => 'logo ml-auto ', 'width' => '300px']) ?>
                    </div>

                    <div class="row">
                        <div class="mx-auto col-lg-12">
                            <?php $form = ActiveForm::begin([
                                'id' => 'login-form',
                                'layout' => 'horizontal',
                                'fieldConfig' => [
                                    'horizontalCssClasses' => ['wrapper' => 'col-lg-12 mx-auto'],
                                ],
                            ]); ?>

                                <?= $form->field($usuario, 'nombre')->textInput(['placeholder' => 'nombre'])->label(false) ?>
                                <?= $form->field($usuario, 'apellidos')->textInput(['placeholder' => 'apellidos'])->label(false) ?>
                                <?= $form->field($usuario, 'login')->textInput(['autofocus' => true, 'placeholder' => 'username'])->label(false) ?>
                                <?= $form->field($usuario, 'password')->passwordInput(['placeholder' => 'contraseña'])->label(false) ?>
                                <?= $form->field($usuario, 'password_repeat')->passwordInput(['placeholder' => 'repetir contraseña'])->label(false) ?>
                                <?= $form->field($usuario, 'email')->textInput(['placeholder' => 'email'])->label(false) ?>
                                <?= $form->field($usuario, 'fnac')->textInput(['placeholder' => 'fecha nac'])->label(false) ?>
                                <?= $form->field($usuario, 'rol')->textInput(['placeholder' => 'rol'])->label(false) ?>

                                <div class="form-group">
                                    <?= Html::submitButton('Registrarse', ['class' => 'mx-auto btn btn-warning btn-block rounded', 'name' => 'register-button']) ?>
                                </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
