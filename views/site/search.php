<?php

/* @var $this yii\web\View */

use yii\bootstrap4\Html;

$this->title = 'My Yii Application';

$js = <<<'EOT'
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
<div class="site-search">

    <div class="owl-carousel my-5">
        <div> <?= Html::img('@web/img/banner.png'); ?> </div>
        <div> <?= Html::img('@web/img/banner2.png'); ?> </div>
        <div> <?= Html::img('@web/img/banner3.png'); ?> </div>
        <div> <?= Html::img('@web/img/banner4.png'); ?> </div>
        <div> <?= Html::img('@web/img/banner5.png'); ?> </div>
    </div>

    <?= Html::beginForm(['site/index'], 'get') ?>
                <div class="form-group">
                    <?= Html::textInput('cadena', $cadena, ['class' => 'form-control']) ?>
                </div>
                <div class="form-group mt-2">
                    <?= Html::submitButton('Buscar', ['class' => 'btn main-yellow']) ?>
                </div>
    <?= Html::endForm() ?>

    <?php if ($usuariosSearch->totalCount > 0) : ?>
            <h3 class="mt-3">Artistas</h3>
            <div class="row">
                <?php foreach ($usuariosSearch->getModels() as $usuario) : ?>
                    <?= Html::a(
                        '<div class="col text-center">' .
                            Html::img($usuario->url_image, ['width' => '150px', 'class' => 'd-inline-block user-search-img']) .
                            '<p class="mt-1 mb-0 font-weight-bold">' . $usuario->login . '</p>' .
                            '<small class="small-artist">artista</small>' .
                        '</div>',
                        ['usuarios/perfil', 'id' => $usuario->id],
                        ['class' => 'perfil-link mt-3']
                    ) ?>
                <?php endforeach; ?>
            </div>
    <?php endif ?>

    <?php if ($cancionesSearch->totalCount > 0) : ?>
            <h3 class="mt-3">Canciones</h3>
            <div class="row">
                <?php foreach ($cancionesSearch->getModels() as $cancion) : ?>
                <div class="col-12 mt-3">
                    <div class="row">
                        <div class="col-1">
                            <?= Html::img($cancion->url_portada, ['width' => '80px', 'class' => 'd-inline-block']) ?>
                        </div>
                        <div class="col my-auto">
                            <p class="m-0 font-weight-bold"><?= $cancion->titulo ?></p>
                            <p class="m-0"><?= $cancion->getUsuario()->one()->login ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
    <?php endif ?>

</div>
