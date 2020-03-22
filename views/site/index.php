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
        <div class="col-6 mt-5">
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
        <div class="col-4 mt-5 ml-auto">
            <div class="row">
                <div class="col-3">
                    <?= Html::img('https://pixabay.com/get/57e7d7444e5aaa14f1dc8460825668204022dfe05b55794a712c79d1/delete-1727486_640.png', ['class' => 'img-fluid']) ?>
                </div>
                <div class="col">
                    <p class="mt-0 p-0"><?= Yii::$app->user->identity->login ?></p>
                    <p class="p-0 m-0"><?= Yii::$app->user->identity->nombre . ' ' .  Yii::$app->user->identity->apellidos ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
