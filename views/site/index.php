<?php

/* @var $this yii\web\View */

use yii\bootstrap4\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$this->title = 'My Yii Application';

$js = <<<EOT
    $(document).ready(function(){
        $(".owl-carousel").owlCarousel({
            loop: true,
            autoplay:true,
            autoplayTimeout:4000,
            items : 1
        });
    });
EOT;

$this->registerJS($js);

?>
<div class="site-index">

    <div class="owl-carousel my-5">
        <div> <?= Html::img('@web/img/banner.png'); ?> </div>
        <div> <?= Html::img('@web/img/banner2.png'); ?> </div>
        <div> <?= Html::img('@web/img/banner3.png'); ?> </div>
        <div> <?= Html::img('@web/img/banner4.png'); ?> </div>
        <div> <?= Html::img('@web/img/banner5.png'); ?> </div>
    </div>

    <?= Html::beginForm(['site/index'], 'get') ?>
                <div class="form-group">
                    <?= Html::textInput('cadena', $cadena, ['class' => 'form-control', 'placeholder' => 'Buscar...']) ?>
                </div>
                <div class="form-group">
                    <?= Html::submitButton('Buscar', ['class' => 'btn main-yellow']) ?>
                </div>
    <?= Html::endForm() ?>

    <div class="row">
        <div class="col-12 col-lg-6 mt-5 order-1 order-lg-0">
            <?php foreach ($canciones as $cancion) : ?>
                <div class="row">
                    <div class="col">
                        <?= Html::img($cancion->url_portada, ['class' => 'img-fluid']) ?>
                        <p class="text-white mb-0"><?= $cancion->titulo?></p>
                        <p class="text-muted mt-0"><?= $cancion->getAlbum()->one()->titulo?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="d-none d-md-block col-lg-6 mt-5 order-0 order-lg-1">
            <div class="row ml-lg-5 user-info">
                <div class="col-md-2 col-lg-3 p-0">
                    <?= Html::img($usuario->url_image, ['class' => 'img-fluid']) ?>
                </div>
                <div class="col-md-10 col-lg-9 my-auto">
                    <p><?= Yii::$app->user->identity->login ?></p>
                    <p><?= Yii::$app->user->identity->nombre . ' ' .  Yii::$app->user->identity->apellidos ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
