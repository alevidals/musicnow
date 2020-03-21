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

    <div class="row mt-5">
        <?php foreach ($canciones as $cancion) : ?>
            <div class="col-12 col-md-6 col-lg-4">
                <?= Html::img($cancion->url_portada, ['class' => 'img-fluid my-2']) ?>
                <p class="text-white mb-0"><?= $cancion->titulo?></p>
                <p class="text-muted mt-0"><?= $cancion->getAlbum()->one()->titulo?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
