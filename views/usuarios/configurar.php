<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\RegistrarForm */

use kartik\datecontrol\DateControl;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ConfigureProfile');
?>
<div class="site-configurar">

    <div class="d-flex">
        <div id="sidebar" class="mr-3">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <div class="nav-link mx-auto">
                    <button class="btn btn-toggle-sidebar"><i class="fas fa-times"></i></button>
                </div>
                <a class="nav-link mx-auto active" id="v-pills-update-tab" data-toggle="pill" href="#v-pills-update" role="tab"
                    aria-controls="v-pills-update" aria-selected="true">
                    <i class="fas fa-id-card"></i><span class="ml-3"><?= Yii::t('app', 'ProfileEdit') ?></span>
                </a>
                <a class="nav-link mx-auto" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile"
                    role="tab" aria-controls="v-pills-profile" aria-selected="false">
                    <i class="fas fa-user-cog"></i><span class="ml-3"><?= Yii::t('app', 'AccountSettings') ?></span>
                </a>
            </div>
        </div>
        <div class="tab-content w-100" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="v-pills-update" role="tabpanel"
                aria-labelledby="v-pills-update-tab">
                    <?= $this->render('update', [
                        'model' => $model,
                    ]) ?>
            </div>
            <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                <?= $this->render('_configurar-settings', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>

</div>
