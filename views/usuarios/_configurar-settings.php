<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\RegistrarForm */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = Yii::t('app', 'ConfigureProfile');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-configurar-settings">
    <div class="row">
        <div class="col-12 mb-3">
                <p class="d-md-inline-block my-0"><?= Yii::t('app', 'DeleteProfileImage') ?></p>
                <?= Html::a(
                    Yii::t('app', 'Delete'),
                    ['usuarios/eliminar-imagen', 'id' => $model->id],
                    [
                        'data' => ['confirm' => Yii::t('app', 'Are you sure you want to delete this item?'), 'method' => 'post'],
                        'role' => 'button',
                        'class' => 'btn btn-sm btn-danger float-md-right'
                    ]
                ) ?>
        </div>
        <div class="col-12 mb-3">
                <p class="d-md-inline-block my-0"><?= Yii::t('app', 'DeleteProfileBanner') ?></p>
                <?= Html::a(
                    Yii::t('app', 'Delete'),
                    ['usuarios/eliminar-banner', 'id' => $model->id],
                    [
                        'data' => ['confirm' => Yii::t('app', 'Are you sure you want to delete this item?'), 'method' => 'post'],
                        'role' => 'button',
                        'class' => 'btn btn-sm btn-danger float-md-right'
                    ]
                ) ?>
        </div>
        <div class="col-12 mb-3">
                <p class="d-md-inline-block my-0"><?= Yii::t('app', 'DeleteAccount') ?></p>
                <?= Html::a(
                    Yii::t('app', 'Delete'),
                    ['usuarios/eliminar-cuenta', 'id' => $model->id],
                    [
                        'data' => ['confirm' => Yii::t('app', 'Are you sure you want to delete this item?'), 'method' => 'post'],
                        'role' => 'button',
                        'class' => 'btn btn-sm btn-danger float-md-right'
                    ]
                ) ?>
        </div>
        <div class="col-12 mb-3">
            <p class="d-md-inline-block my-0"><?= Yii::t('app', 'Language') ?></p>
            <div class="dropdown float-md-right">
                <button class="btn btn-sm main-yellow dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $_COOKIE['lang'] ?>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="/index.php?r=site%2Fidioma&amp;lang=es-ES">Español</a>
                    <a class="dropdown-item" href="/index.php?r=site%2Fidioma&amp;lang=en">English</a>
                </div>
            </div>
        </div>
    </div>

</div>