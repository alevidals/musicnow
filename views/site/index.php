<?php

/* @var $this yii\web\View */

use yii\bootstrap4\Html;

$this->title = 'My Yii Application';

$sort = Yii::$app->request->get('sort');

$js = <<<EOT

    $(document).ready(function(){
        setTimeout(function () {
            $(".owl-carousel-index").owlCarousel({
                loop: true,
                autoplay:true,
                autoplayTimeout:4000,
                items : 1
            });
        }, 500);
    });
EOT;

$this->registerJS($js);

?>
<div class="site-index">

    <div class="owl-carousel owl-carousel-index my-5">
        <div> <?= Html::img('@web/img/banner.png', ['alt' => 'banner']); ?> </div>
        <div> <?= Html::img('@web/img/banner2.png', ['alt' => 'banner2']); ?> </div>
        <div> <?= Html::img('@web/img/banner3.png', ['alt' => 'banner3']); ?> </div>
        <div> <?= Html::img('@web/img/banner4.png', ['alt' => 'banner4']); ?> </div>
        <div> <?= Html::img('@web/img/banner5.png', ['alt' => 'banner5']); ?> </div>
    </div>

    <?= Html::beginForm(['site/index'], 'get', ['data-pjax' => 'true']) ?>
        <div class="form-group">
            <?= Html::textInput('cadena', $cadena, ['class' => 'form-control', 'placeholder' => Yii::t('app', 'Search') . '...', 'autocomplete' => 'off']) ?>
        </div>
        <div class="form-group">
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
                <?= Html::a(Yii::t('app', 'Artist'), ['index', 'cadena' => $cadena, 'sort' => ($sort[0] == '-' ? 'u.login' : '-u.login')], ['class' => 'dropdown-item']) ?>
                <?= Html::a(Yii::t('app', 'Género'), ['index', 'cadena' => $cadena, 'sort' => ($sort[0] == '-' ? 'g.denominacion' : '-g.denominacion')], ['class' => 'dropdown-item']) ?>
                <?= Html::a('Likes', ['index', 'cadena' => $cadena, 'sort' => ($sort[0] == '-' ? 'likes' : '-likes')], ['class' => 'dropdown-item']) ?>
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

    <section class="row">
        <div class="col-12 col-lg-6 mt-5 order-1 order-lg-0 canciones-container">
            <?php foreach ($canciones as $cancion) : ?>
                <article class="card mb-3" itemscope itemtype="https://schema.org/MusicRecording">
                    <div class="card-header">
                        <?= Html::a(
                            Html::img($cancion->getUsuario()->one()->url_image, ['class' => 'user-search-img', 'width' => '40px', 'alt' => 'logo', 'itemprop' => 'image']) .
                            '<span class="ml-3" itemprop="name">' . $cancion->getUsuario()->one()->login . '</span>',
                            ['usuarios/perfil', 'id' => $cancion->usuario_id],
                            ['itemprop' => 'byArtist', 'itemscope' => '', 'itemtype' => 'https://schema.org/Person']
                        ) ?>
                    </div>
                    <div class="card-body py-0">
                        <div class="row">
                            <div class="col">
                                <div class="song-container mt-3">
                                    <div class="box-3">
                                        <?= Html::img($cancion->url_portada, ['class' => 'img-fluid', 'alt' => 'portada', 'itemprop' => 'image'])?>
                                        <div class="share-buttons">
                                            <button id="play-<?= $cancion->id ?>" class="action-btn play-btn outline-transparent"><i class="fas fa-play"></i></button>
                                            <button id="outerlike-<?= $cancion->id ?>" class="action-btn outline-transparent bubbly-button like-btn"><i class="<?= in_array($cancion->id, $likes) ? 'fas' : 'far' ?> fa-heart red-hearth"></i></button>
                                            <button class="action-btn outline-transparent cancion" data-toggle="modal" data-target="#song-<?= $cancion->id ?>"><i class="far fa-comment"></i></button>
                                            <button data-song="<?= $cancion->id ?>" class="action-btn outline-transparent add-btn"><i class="fas fa-music"></i></button>
                                            <button data-song="<?= $cancion->id ?>" data-user="<?= Yii::$app->user->id ?>" class="action-btn outline-transparent playlist-btn" data-toggle="modal" data-target="#playlist"><i class="fas fa-plus"></i></button>
                                        </div>
                                        <div class="layer"></div>
                                    </div>
                                </div>
                                <div class="w-100 my-3 text-truncate">
                                    <h4 class="my-2" itemprop="name"><?= Html::encode($cancion->titulo) ?></h4>
                                    <?php if ($cancion->album_id !== null) : ?>
                                        <div class="my-2">
                                            <div class="w-100"></div>
                                            <span itemprop="inAlbum"><?= Html::encode($cancion->getAlbum()->one()->titulo) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($cancion->explicit) : ?>
                                        <div class="my-2">
                                            <div class="w-100"></div>
                                            <span class="badge explicit-badge">EXPLICIT</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="modal fade" id="song-<?= $cancion->id ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><?= $cancion->titulo ?></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-lg-8">
                                                        <div class="row">
                                                            <?= Html::img($cancion->url_portada, ['class' => 'img-fluid col-12', 'alt' => 'profile-image']) ?>
                                                            <div class="col-12 mt-4">
                                                                <textarea id="text-area-comment-<?= $cancion->id ?>" class="form-control text-area-comment" cols="30" rows="3" placeholder="<?= Yii::t('app', 'Comment') . '...' ?>"></textarea>
                                                                <div class="invalid-feedback"><?= Yii::t('app', 'MaxChar') ?></div>
                                                                <div class="mt-3">
                                                                    <button class="btn btn-sm main-yellow comment-btn" id="comment-<?= $cancion->id ?>" type="button"><?= Yii::t('app', 'CommentAction') ?></button>
                                                                    <button type="button" id="like-<?= $cancion->id ?>" class="btn-lg outline-transparent d-inline-block like-btn p-0 mx-2"><i class="fa-heart red-hearth"></i></button>
                                                                    <p class="d-inline-block"><span></span> like/s</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 ">
                                                        <div class="row">
                                                            <div class="col-12 custom-overflow">
                                                                <!-- COMENTARIOS  -->
                                                                <div class="row row-comments">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
            <div class="modal fade" id="playlist" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <h2 class="text-center">Playlists</h2>
                            <div class="row row-playlists">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a class="perfil-link" href="/index.php?r=usuarios%2Fperfil&id=<?= Yii::$app->user->id ?>">
            <div class="d-none d-md-block col-lg-6 mt-5 order-0 order-lg-1">
                <div class="row ml-lg-5 user-info">
                    <div class="col-md-2 col-lg-3 p-0">
                        <?= Html::img($usuario->url_image, ['class' => 'img-fluid user-search-img', 'alt' => 'user-image']) ?>
                    </div>
                    <div class="col-md-10 col-lg-9 my-auto">
                        <p><?= Html::encode(Yii::$app->user->identity->login) ?></p>
                        <p><?= Html::encode(Yii::$app->user->identity->nombre) . ' ' . Html::encode(Yii::$app->user->identity->apellidos) ?></p>
                    </div>
                </div>
            </div>
        </a>
    </section>
</div>
