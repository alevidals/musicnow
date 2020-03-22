<?php

/* @var $this yii\web\View */

use yii\bootstrap4\Html;

$this->title = 'My Yii Application';

$js = <<<EOT
    $(document).ready(function(){
        $(".owl-carousel").owlCarousel({
            'loop': true,
            autoplay:true,
            autoplayTimeout:4000,
            items : 1
        });
    });
EOT;

$this->registerJS($js);

?>
<div class="site-index">

    <div class="owl-carousel mt-5">
        <div> <?= Html::img('@web/img/banner.png'); ?> </div>
        <div> <?= Html::img('@web/img/banner2.png'); ?> </div>
        <div> <?= Html::img('@web/img/banner3.png'); ?> </div>
        <div> <?= Html::img('@web/img/banner4.png'); ?> </div>
        <div> <?= Html::img('@web/img/banner5.png'); ?> </div>
    </div>

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
                <div class="col-md-2 col-lg-3 col-xl-2">
                    <?= Html::img('https://pixabay.com/get/57e7d7444e5aaa14f1dc8460825668204022dfe05b55794a712c79d1/delete-1727486_640.png', ['class' => 'img-fluid', ['max-width' => '100px']]) ?>
                </div>
                <div class="col-md-10 col-lg-9 col-xl-10 my-auto">
                    <p><?= Yii::$app->user->identity->login ?></p>
                    <p><?= Yii::$app->user->identity->nombre . ' ' .  Yii::$app->user->identity->apellidos ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
