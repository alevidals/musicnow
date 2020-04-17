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

$sort = Yii::$app->request->get('sort');

$this->registerJS($js);

?>
<div class="site-search">

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
                <div class="form-group mt-2">
                    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn main-yellow']) ?>
                </div>
    <?= Html::endForm() ?>

    <?php if ($usuariosSearch->totalCount > 0) : ?>
            <h3 class="mt-3"><?= Yii::t('app', 'Artists') ?></h3>
            <div class="row">
                <?php foreach ($usuariosSearch->getModels() as $usuario) : ?>
                    <?= Html::a(
    '<div class="col text-center">' .
                            Html::img($usuario->url_image, ['width' => '150px', 'class' => 'd-inline-block user-search-img', 'alt' => 'profile-image']) .
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
        <h3 class="my-3"><?= Yii::t('app', 'Songs') ?></h3>
        <div class="dropdown">
            <button class="btn main-yellow dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= Yii::t('app', 'Order') ?>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <?= Html::a(Yii::t('app', 'Artist'), ['site/search', 'cadena' => $cadena, 'sort' => ($sort[0] == '-' ? 'u.login' : '-u.login')], ['class' => 'dropdown-item']) ?>
                <?= Html::a(Yii::t('app', 'Género'), ['site/search', 'cadena' => $cadena, 'sort' => ($sort[0] == '-' ? 'g.denominacion' : '-g.denominacion')], ['class' => 'dropdown-item']) ?>
                <?= Html::a('Likes', ['site/search', 'cadena' => $cadena, 'sort' => ($sort[0] == '-' ? 'likes' : '-likes')], ['class' => 'dropdown-item']) ?>
            </div>
        </div>
        <div class="row">
            <?php foreach ($cancionesSearch->getModels() as $cancion) : ?>
            <div class="col-12 mt-3">
                <div class="row">
                    <div class="col-3 col-md-2 col-lg-1">
                        <?= Html::img($cancion->url_portada, ['class' => 'img-fluid', 'alt' => 'portada']) ?>
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