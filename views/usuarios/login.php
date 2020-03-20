<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\models\Usuarios;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

$usuario = new Usuarios(['scenario' => Usuarios::SCENARIO_CREAR]);

$js = <<<'EOT'
var current_fs, next_fs, previous_fs; //fieldsets
var left, opacity, scale; //fieldset properties which we will animate
var animating; //flag to prevent quick multi-click glitches

$(".next").click(function(){

    current_fs = $(this).parent();
    next_fs = $(this).parent().next();

    //show the next fieldset
    current_fs.fadeOut(500, function () {
        next_fs.fadeIn(500);
    });
	//hide the current fieldset with style
});

$(".previous").click(function(){

	current_fs = $(this).parent();
	previous_fs = $(this).parent().prev();

	//show the previous fieldset
    current_fs.fadeOut(500, function () {
        previous_fs.fadeIn();
    });
	//hide the current fieldset with style
});
EOT;

$this->registerJS($js);

$this->title = 'Login';

?>
<div class="site-login container">

    <div class="text-center mb-5">
        <?= Html::img('@web/img/music_now_letras.png', ['alt' => Yii::$app->name, 'class' => 'logo ml-auto', 'width' => '300px']) ?>
    </div>

    <ul class="nav nav-tabs justify-content-center nav-fill mb-5 w-100" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active text-uppercase" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="true"><?= Yii::t('app', 'Entrar') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-uppercase" id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="false"><?= Yii::t('app', 'Registrarse') ?></a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
            <div class="row">

                <div class="col-lg-6 align-self-center px-0">

                    <div class="row">
                        <div class="mx-auto col-lg-12">
                            <?php $form = ActiveForm::begin([
                                'id' => 'login-form',
                                'layout' => 'horizontal',
                                'fieldConfig' => [
                                    'horizontalCssClasses' => ['wrapper' => 'col-lg-12 mx-auto'],
                                ],
                            ]); ?>
                                <h2 class="fs-title"><?= Yii::t('app', 'Inicia sesión con tu nombre de usuario') ?></h2>
                                <?= $form->field($loginFormModel, 'username')->textInput(['autofocus' => true])->label(Yii::t('app', 'Nombre de usuario'), ['class' => 'col-12']) ?>
                                <?= $form->field($loginFormModel, 'password')->passwordInput()->label(Yii::t('app', 'Password')) ?>
                                <?= $form->field($loginFormModel, 'rememberMe')->checkbox()->label(Yii::t('app', 'Remember me')) ?>

                                <div class="form-group row px-15">
                                    <?= Html::submitButton('Login', ['class' => 'btn btn-warning btn-block rounded', 'name' => 'login-button']) ?>
                                </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <?= Html::img('@web/img/undraw_listening_1u79.png', ['alt' => 'girl-music', 'class' => ' img-fluid mt-4']) ?>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
            <div class="row">
                <div class="col-lg-6 order-2 order-lg-1 align-self-center">
                    <?= Html::img('@web/img/undraw_compose_music_ovo2.png', ['alt' => 'girl-music', 'class' => 'img-fluid mt-4']) ?>
                </div>
                <div class="col-lg-6 order-1 order-lg-2 align-self-center px-0">

                    <div class="row">
                        <div class="mx-auto col-lg-12">
                            <?php $form = ActiveForm::begin([
                                'id' => 'register-form',
                                'layout' => 'horizontal',
                                'action' => ['usuarios/registrar'],
                                'fieldConfig' => [
                                    'horizontalCssClasses' => ['wrapper' => 'col-lg-12 mx-auto'],
                                ],
                            ]); ?>
                                <fieldset>
                                    <h2 class="fs-title"><?= Yii::t('app', 'Regístrate con tu dirección de correo electrónico') ?></h2>
                                    <?= $form->field($usuario, 'nombre')->textInput()->label(Yii::t('app', 'Nombre') . '*', ['class' => 'col-12']) ?>
                                    <?= $form->field($usuario, 'apellidos')->textInput()->label(Yii::t('app', 'Apellidos') . '*', ['class' => 'col-12']) ?>
                                    <?= $form->field($usuario, 'login')->textInput(['autofocus' => true])->label(Yii::t('app', 'Nombre de usuario') . '*', ['class' => 'col-12']) ?>
                                    <?= Html::button(Yii::t('app', 'Siguiente'), ['type' => 'button', 'name' => 'next', 'class' => 'next action-button btn main-yellow']) ?>
                                </fieldset>
                                <fieldset>
                                    <h2 class="fs-title"><?= Yii::t('app', 'Regístrate con tu dirección de correo electrónico') ?></h2>
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <?= $form->field($usuario, 'password')->passwordInput()->label(Yii::t('app', 'Password') . '*', ['class' => 'col-12']) ?>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <?= $form->field($usuario, 'password_repeat')->passwordInput()->label(Yii::t('app', 'Password Repeat') . '*', ['class' => 'col-12']) ?>
                                        </div>
                                    </div>
                                    <?= $form->field($usuario, 'email')->textInput()->label('Email*', ['class' => 'col-12']) ?>
                                    <?= $form->field($usuario, 'fnac')->textInput()->label(Yii::t('app', 'Fnac'), ['class' => 'col-12']) ?>
                                    <?= $form->field($usuario, 'rol')->textInput()->label('Rol', ['class' => 'col-12']) ?>
                                    <?= Html::button(Yii::t('app', 'Anterior'), ['type' => 'button', 'name' => 'previous', 'class' => 'previous action-button-previous btn main-yellow']) ?>
                                    <?= Html::submitButton(Yii::t('app', 'Registrarse'), ['class' => ' btn main-yellow rounded', 'name' => 'register-button']) ?>
                                </fieldset>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>