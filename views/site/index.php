<?php

/* @var $this yii\web\View */

use app\services\Utility;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = 'My Yii Application';

$playSongCode = Utility::PLAY_SONG;

$urlLike = Url::to(['likes/like']);
$urlGetLikesData = Url::to(['likes/get-data']);
$urlComment = Url::to(['comentarios/comentar']);
$urlGetComments = Url::to(['canciones/comentarios']);
$urlPerfil = Url::to(['usuarios/perfil']);
$urlGetSongData = Url::to(['canciones/get-song-data']);

$likeCommentProfile = Utility::LIKE_COMMENT_PROFILE;

$js = <<<EOT

    $(document).ready(function(){
        $(".owl-carousel").owlCarousel({
            loop: true,
            autoplay:true,
            autoplayTimeout:4000,
            items : 1
        });
    });

    $likeCommentProfile
    // CÓDIGO PARA REPRODUCIR LA CANCIÓN
    $playSongCode

    $('.add-btn').on('click', function ev() {
        var cancion_id = $(this).data('song');
        $.ajax({
            method: 'GET',
            url: '$urlGetSongData&cancion_id=' + cancion_id,
            success: function (data) {
                songs.push({
                    url_cancion: data.url_cancion,
                    url_portada: data.url_portada,
                    titulo: data.titulo,
                    album: data.album,
                });
                var audio = document.getElementById('audio');
                var source = $('.player audio source')[0];
                if (audio.paused) {
                    if ($('.loading').length) {
                        $('.loading').remove();
                        $('.play-pause-btn').remove();
                        $('.controls').remove();
                        $('.volume').remove();
                        $('.download').remove();
                    }
                    GreenAudioPlayer.init({
                        selector: '.player',
                        stopOthersOnPlay: true,
                        showDownloadButton: true,
                    });
                    var cancion = songs.shift();
                    $('.info-song img').attr('src', cancion.url_portada);
                    $('.player audio source').attr('src', cancion.url_cancion);
                    $('.artist-info p').html(cancion.titulo);
                    $('.artist-info small').html(cancion.album);
                    $('.full-player').css('display', 'flex');
                    $('.player').css('display', 'flex');
                    var audio = document.getElementById('audio');
                    audio.addEventListener('ended', () =>  {
                        if (songs.length > 0) {
                            var cancion = songs.shift();
                            $('.info-song img').attr('src', cancion.url_portada);
                            $('.player audio source').attr('src', cancion.url_cancion);
                            $('.artist-info p').html(cancion.titulo);
                            $('.artist-info small').html(cancion.album);
                            $('.play-pause-btn').trigger('click');
                        }
                    });
                }
            }
        });
    });
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
                        <div class="song-container">
                            <div class="box-3">
                                <?= Html::img($cancion->url_portada, ['class' => 'img-fluid', 'alt' => 'portada'])?>
                                <div class="share-buttons">
                                    <button id="play-<?= $cancion->id ?>" class="action-btn play-btn outline-transparent"><i class="fas fa-play"></i></button>
                                    <button id="outerlike-<?= $cancion->id ?>" class="action-btn outline-transparent bubbly-button like-btn"><i class="<?= in_array($cancion->id, $likes) ? 'fas' : 'far' ?> fa-heart text-danger"></i></button>
                                    <button class="action-btn outline-transparent cancion" data-toggle="modal" data-target="#song-<?= $cancion->id ?>"><i class="far fa-comment"></i></button>
                                    <button data-song="<?= $cancion->id ?>" class="action-btn outline-transparent add-btn"><i class="fas fa-plus"></i></button>
                                </div>
                                <div class="layer"></div>
                            </div>
                        </div>
                        <h4 class="text-center mt-3 mb-5"><?= $cancion->titulo ?></h4>
                        <div class="modal fade" id="song-<?= $cancion->id ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
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
                                                            <button type="button" id="like-<?= $cancion->id ?>" class="btn-lg outline-transparent d-inline-block like-btn p-0 mx-2"><i class="fa-heart text-danger"></i></button>
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
            <?php endforeach; ?>
        </div>
        <a class="perfil-link" href="/index.php?r=usuarios%2Fperfil&id=<?= Yii::$app->user->id ?>">
            <div class="d-none d-md-block col-lg-6 mt-5 order-0 order-lg-1">
                <div class="row ml-lg-5 user-info">
                    <div class="col-md-2 col-lg-3 p-0">
                        <?= Html::img($usuario->url_image, ['class' => 'img-fluid user-search-img', 'alt' => 'user-image']) ?>
                    </div>
                    <div class="col-md-10 col-lg-9 my-auto">
                        <p><?= Yii::$app->user->identity->login ?></p>
                        <p><?= Yii::$app->user->identity->nombre . ' ' . Yii::$app->user->identity->apellidos ?></p>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
