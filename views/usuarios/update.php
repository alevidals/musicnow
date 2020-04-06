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
<div class="usuarios-update">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row justify-content-center mt-5">
        <div class="col-lg-6 text-center">
            <div class="form-group field-usuarios-image">
                <label class="img-edit" for="usuarios-image">
                    <img class="user-search-img" src="<?= $model->url_image ?>" alt="profile-img">
                    <i class="fas fa-pen edit-image-icon"></i>
                </label>
                <input class="file-input form-control-file d-none" type="file" name="Usuarios[image]" id="usuarios-image">
                <h5 class="mt-3"><?= $model->login ?></h5>
                <h6><?= $model->nombre . ' ' . $model->apellidos ?></h6>
            </div>
        </div>

        <div class="w-100"></div>

        <div class="col-lg-6">
            <?= $form->field($model, 'login')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="w-100"></div>

        <div class="col-lg-3">
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'apellidos')->textInput(['maxlength' => true]) ?>
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
