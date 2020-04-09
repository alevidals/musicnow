<?php

/* @var $this yii\web\View */

use yii\bootstrap4\Html;

$this->title = 'My Yii Application';

?>
<div class="site-admin-index">

    <h1 class="text-center my-5"><?= Yii::t('app', 'AdministrationPanel') ?></h1>

    <div class="row justify-content-center align-content-center">
        <div class="col-lg-3 text-center">
            <?= Html::a(
                '<i class="fas fa-users"></i>
                <h4 class="mt-3">' . Yii::t('app', 'Usuarios') . '</h4>',
                ['usuarios/index'],
                ['class' => 'perfil-link']
            ) ?>
        </div>
        <div class="col-lg-3 text-center">
            <?= Html::a(
                '<i class="fas fa-user-tag"></i>
                <h4 class="mt-3">' . Yii::t('app', 'Rol') . '</h4>',
                ['roles/index'],
                ['class' => 'perfil-link']
            ) ?>
        </div>
        <div class="col-lg-3 text-center">
            <?= Html::a(
                '<i class="fas fa-comment"></i>
                <h4 class="mt-3">' . Yii::t('app', 'Comments') . '</h4>',
                ['comentarios/index'],
                ['class' => 'perfil-link']
            ) ?>
        </div>
        <div class="col-lg-3 text-center">
            <?= Html::a(
                '<i class="fas fa-heart"></i>
                <h4 class="mt-3">Likes</h4>',
                ['likes/index'],
                ['class' => 'perfil-link']
            ) ?>
        </div>
        <div class="w-100 my-3"></div>
        <div class="col-lg-3 text-center">
            <?= Html::a(
                '<i class="fas fa-music"></i>
                <h4 class="mt-3">' . Yii::t('app', 'Canciones') . '</h4>',
                ['canciones/index'],
                ['class' => 'perfil-link']
            ) ?>
        </div>
        <div class="col-lg-3 text-center">
            <?= Html::a(
                '<i class="fas fa-compact-disc"></i>
                <h4 class="mt-3">' . Yii::t('app', 'Albumes') . '</h4>',
                ['albumes/index'],
                ['class' => 'perfil-link']
            ) ?>
        </div>
        <div class="col-lg-3 text-center">
            <?= Html::a(
                '<i class="fas fa-file-audio"></i>
                <h4 class="mt-3">' . Yii::t('app', 'Generos') . '</h4>',
                ['generos/index'],
                ['class' => 'perfil-link']
            ) ?>
        </div>
        <div class="col-lg-3 text-center">
            <?= Html::a(
                '<i class="fas fa-user-friends"></i>
                <h4 class="mt-3">' . Yii::t('app', 'Followers') . '</h4>',
                ['seguidores/index'],
                ['class' => 'perfil-link']
            ) ?>
        </div>
    </div>
</div>
