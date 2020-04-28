<?php

use kartik\datecontrol\DateControl;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = Yii::t('app', 'Update Usuarios: {name}', [
    'name' => $model->id,
]);
// $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Usuarios'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
// $this->params['breadcrumbs'][] = Yii::t('app', 'Update');

?>
<div class="usuarios-update container">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row justify-content-center mt-5">
        <div clss="col-lg-6 text-center">
            <div class="form-group field-usuarios-banner">
                <label class="img-edit" for="usuarios-banner">
                    <?php if ($model->url_banner) : ?>
                        <img class="user-search-banner img-fluid" src="<?= $model->url_banner ?>" alt="Banner">
                        <i class="fas fa-pen edit-image-icon-banner"></i>
                    <?php else : ?>
                        <img class="user-search-banner img-fluid" src=":" alt="Banner">
                        <i class="fas fa-pen edit-image-icon-banner right-50"></i>
                    <?php endif; ?>
                </label>
                <?= $form->field($model, 'banner')->fileInput(['class' => 'file-input-banner form-control-file d-none'])->label(false) ?>
            </div>
    </div>

    <div class="row justify-content-center mt-5">
        <div clss="col-lg-6 text-center">
            <div class="form-group field-usuarios-image">
                <label class="img-edit" for="usuarios-image">
                    <img class="user-search-img profile-img img-fluid" src="<?= $model->url_image ?>" alt="Image">
                    <i class="fas fa-pen edit-image-icon-image"></i>
                </label>
                <?= $form->field($model, 'image')->fileInput(['class' => 'file-input form-control-file d-none'])->label(false) ?>
                <h5 class="mt-3 update-login"><?= Html::encode($model->login) ?></h5>
                <h6><span class="update-nombre"><?= Html::encode($model->nombre)?></span> <span class="update-apellidos"><?= Html::encode($model->apellidos)?></span></h6>
            </div>
        </div>


        <div class="w-100"></div>

        <div class="col-lg-6">
            <?= $form->field($model, 'login')->textInput(['maxlength' => true, 'data-target' => 'login']) ?>
        </div>

        <div class="w-100"></div>

        <div class="col-lg-3">
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true, 'data-target' => 'nombre']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'apellidos')->textInput(['maxlength' => true, 'data-target' => 'apellidos']) ?>
        </div>

        <div class="w-100"></div>

        <div class="col-lg-3">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'fnac')->textInput()
            ->widget(
                DateControl::classname(),
                [
                    'type' => DateControl::FORMAT_DATE,
                    'displayFormat' => 'php:d-m-Y',
                ]
            ); ?>
        </div>

        <div class="w-100"></div>

        <div class="col-lg-3">
            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true]) ?>
        </div>

        <div class="w-100"></div>

        <div class="col-6 mt-3">
            <div class="form-group text-center">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn main-yellow']) ?>
            </div>
        </div>
    </div>



    <?php ActiveForm::end(); ?>

</div>
