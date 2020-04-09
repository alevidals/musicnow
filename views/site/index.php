<?php

/* @var $this yii\web\View */

use app\services\Utility;
use yii\bootstrap4\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'My Yii Application';

$playSongCode = Utility::PLAY_SONG;

$js = <<<EOT
    $(document).ready(function(){
        $(".owl-carousel").owlCarousel({
            loop: true,
            autoplay:true,
            autoplayTimeout:4000,
            items : 1
        });
    });

    // CÓDIGO PARA REPRODUCIR LA CANCIÓN
    $playSongCode
EOT;

$this->registerJS($js);

?>
<div class="site-index">

    <div class="owl-carousel my-5">
        <div> <?= Html::img('@web/img/banner.png', ['alt' => 'banner']); ?> </div>
        <div> <?= Html::img('@web/img/banner2.png', ['alt' => 'banner2']); ?> </div>
        <div> <?= Html::img('@web/img/banner3.png', ['alt' => 'banner3']); ?> </div>
        <div> <?= Html::img('@web/img/banner4.png', ['alt' => 'banner4']); ?> </div>
        <div> <?= Html::img('@web/img/banner5.png', ['alt' => 'banner5']); ?> </div>
    </div>

    <?= Html::beginForm(['site/index'], 'get') ?>
                <div class="form-group">
                    <?= Html::textInput('cadena', $cadena, ['class' => 'form-control', 'placeholder' => Yii::t('app', 'Search') . '...']) ?>
                </div>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn main-yellow']) ?>
                </div>
    <?= Html::endForm() ?>

    <div class="row">
        <div class="col-12 col-lg-6 mt-5 order-1 order-lg-0">
            <?php foreach ($canciones as $cancion) : ?>
                <div class="row">
                    <div class="col">
                        <?= Html::img($cancion->url_portada, ['class' => 'img-fluid', 'alt' => 'portada']) ?>
                        <div>
                            <button id="play-<?= $cancion->id ?>" class="action-btn play-btn outline-transparent"><i class="fas fa-play"></i></button>
                        </div>
                        <p class="text-white mb-0"><?= $cancion->titulo?></p>
                        <p class="text-muted mt-0"><?= $cancion->getAlbum()->one()->titulo?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="d-none d-md-block col-lg-6 mt-5 order-0 order-lg-1">
            <div class="row ml-lg-5 user-info">
                <div class="col-md-2 col-lg-3 p-0">
                    <?= Html::img($usuario->url_image, ['class' => 'img-fluid', 'alt' => 'user-image']) ?>
                </div>
                <div class="col-md-10 col-lg-9 my-auto">
                    <p><?= Yii::$app->user->identity->login ?></p>
                    <p><?= Yii::$app->user->identity->nombre . ' ' .  Yii::$app->user->identity->apellidos ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
