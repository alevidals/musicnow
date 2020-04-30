<?php

use kartik\datecontrol\DateControl;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = Yii::t('app', 'Update Usuarios: {name}', [
    'name' => $model->id,
]);

?>
<div class="usuarios-update">
    <?= Html::img($model->url_banner, ['class' => 'user-search-banner mb-3 img-fluid']) ?>
    <div class="d-sm-flex mb-3">
        <?= Html::img($model->url_image, ['class' => 'user-search-img profile-img']) ?>
        <div>
            <h5 class="mt-3 update-login"><?= Html::encode($model->login) ?></h5>
            <h6><span class="update-nombre"><?= Html::encode($model->nombre)?></span> <span class="update-apellidos"><?= Html::encode($model->apellidos)?></span></h6>
        </div>
    </div>
    <?php $form = ActiveForm::begin(['action' => ['usuarios/update', 'id' => $model->id]]); ?>
        <ul class="nav nav-pills nav-fill mb-3">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="pill" href="#user-info"><?= Yii::t('app', 'UserInfo') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#image-banner"><?= Yii::t('app', 'ImageBanner') ?></a>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="user-info" role="tabpanel" aria-labelledby="user-info-tab">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <?= $form->field($model, 'login')->textInput(['maxlength' => true, 'data-target' => 'login']) ?>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-12 col-lg-6">
                        <?= $form->field($model, 'nombre')->textInput(['maxlength' => true, 'data-target' => 'nombre']) ?>
                    </div>
                    <div class="col-12 col-lg-6">
                        <?= $form->field($model, 'apellidos')->textInput(['maxlength' => true, 'data-target' => 'apellidos']) ?>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-12 col-lg-6">
                        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-12 col-lg-6">
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
                    <div class="col-12 col-lg-6">
                        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-12 col-lg-6">
                        <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true]) ?>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-12">
                        <?= $form->field($model, 'privated_account')->checkbox() ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="image-banner" role="tabpanel" aria-labelledby="image-banner-tab">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <?= $form->field($model, 'image')->fileInput(['class' => 'file-input']) ?>
                    </div>
                    <div class="col-12">
                        <?= $form->field($model, 'banner')->fileInput(['class' => 'file-input-banner']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div >
            <div class="form-group text-center">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn main-yellow']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
