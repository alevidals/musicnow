<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

\yii\web\YiiAsset::register($this);

$urlFollow = Url::to(['seguidores/follow', 'seguido_id' => $model->id]);
$urlGetFollowData = Url::to(['seguidores/get-data', 'seguido_id' => $model->id]);
$urlLike = Url::to(['likes/like']);
$urlGetLikesData = Url::to(['likes/get-data']);
$urlComment = Url::to(['comentarios/comentar']);
$urlGetComments = Url::to(['canciones/comentarios']);
$urlPerfil = Url::to(['usuarios/perfil']);

$js = <<<EOT

    $.ajax({
        'method': 'POST',
        'url': '$urlGetFollowData',
        success: function (data) {
            $('.follow').html(data.textButton);
            $('#seguidores').html(data.seguidores);
            $('#seguidos').html(data.seguidos);
        }
    });

    $('.follow').on('click', function ev(e) {
        $.ajax({
            'method': 'POST',
            'url': '$urlFollow',
            success: function (data) {
                $('.follow').html(data.textButton);
                $('#seguidores').html(data.seguidores);
            }
        });
    });

    $('.like-btn').on('click', function ev(e) {
        var cancion_id = $(this).attr('id').split('-')[1];
        $.ajax({
            'method': 'POST',
            url: '$urlLike&cancion_id=' + cancion_id,
            success: function (data) {
                if (data.class == 'far') {
                    $('.like-btn i').removeClass('fas');
                    $('.like-btn i').addClass('far');
                } else {
                    $('.like-btn i').removeClass('far');
                    $('.like-btn i').addClass('fas');
                }
                $('.like-btn ~ p span').html(data.likes);
            }
        });
    });

    $('.cancion').on('click', function ev(e) {
        var cancion_id = $(this).data('target').split('-')[1];
        $.ajax({
            'method': 'POST',
            url: '$urlGetLikesData&cancion_id=' + cancion_id,
            success: function (data) {
                $('.like-btn i').addClass(data.class);
                $('.like-btn ~ p span').html(data.likes);
            }
        });

        $.ajax({
            method: 'POST',
            url: '$urlGetComments&cancion_id=' + cancion_id,
            success: function (data) {
                var comentarios = Object.entries(data);
                $('.row-comments').empty();
                comentarios.forEach(element => {
                    $('.row-comments').append(`
                        <div class="col-12 mt-3">
                            <div class="row">
                                <a href="$urlPerfil&id=\${element[1].id}">
                                    <img class="user-search-img" src="\${element[1].url_image}" alt="perfil" width="50px" height="50px">
                                </a>
                                <div class="col">
                                    <a href="$urlPerfil&id=\${element[1].id}">\${element[1].login}</a>
                                    <p class="m-0">\${element[1].comentario}</p>
                                </div>
                            </div>
                        </div>
                    `);
                });
            }
        });

    });

    $('.comment-btn').on('click', function ev(e) {
        var cancion_id = $(this).attr('id').split('-')[1];
        var comentario = $('.text-area-comment').val();
        if (comentario.length > 255 || comentario.length == 0) {
            $('.invalid-feedback').show();
        } else {
            $('.invalid-feedback').hide();
            $.ajax({
                'method': 'POST',
                url: '$urlComment&cancion_id=' + cancion_id,
                data: {
                    comentario: comentario,
                },
                success: function (data) {
                    $('.row-comments').append(`
                        <div class="col-12 mt-3">
                            <div class="row">
                                <a href="$urlPerfil&id=\${data.usuario_id}">
                                    <img class="user-search-img" src="\${data.url_image}" alt="perfil" width="50px" height="50px">
                                </a>
                                <div class="col">
                                    <a href="$urlPerfil&id=\${data.usuario_id}">\${data.login}</a>
                                    <p>\${data.comentario}</p>
                                </div>
                            </div>
                        </div>
                    `);
                    $('.text-area-comment').val('');
                }
            });
        }
    });

EOT;

$this->registerJS($js);

?>

<div class="usuarios-view">

    <?= Html::img('@web/img/banner.png', ['class' => 'img-fluid']) ?>

    <?php if ($model->id != Yii::$app->user->id) : ?>
        <button class="btn main-yellow mt-4 follow"></button>
    <?php endif; ?>

    <div class="row text-white text-center mt-4">
        <div class="col">
            <button class="outline-transparent" type="button" data-toggle="modal" data-target="#seguidores-list">
                <h4><span id="seguidores"></span> seguidores</h4>
            </button>
            <div class="modal fade" id="seguidores-list" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <?php if (count($seguidores) > 0) : ?>
                                    <?php foreach ($seguidores as $usuario) : ?>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col">
                                                    <?= Html::img($model->url_image, ['class' => 'd-inline-block user-search-img my-auto', 'width' => '30px']) ?>
                                                </div>
                                                <div class="col">
                                                    <p class="d-inline-block my-auto"><?= $usuario->login ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div class="col-12">
                                        <p class="my-auto">Parece que no te sigue nadie aún.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <button class="outline-transparent" type="button" data-toggle="modal" data-target="#seguidos-list">
                <h4><span id="seguidos"></span> seguidos</h4>
            </button>
            <div class="modal fade" id="seguidos-list" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <?php if (count($seguidos) > 0) : ?>
                                    <?php foreach ($seguidos as $usuario) : ?>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col">
                                                    <?= Html::img($model->url_image, ['class' => 'd-inline-block user-search-img my-auto', 'width' => '30px']) ?>
                                                </div>
                                                <div class="col">
                                                    <p class="d-inline-block my-auto"><?= $usuario->login ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div class="col-12">
                                        <p class="my-auto">Parece que no sigues a nadie aún.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= Html::img($model->url_image, ['width' => '100px', 'id' => 'image-perfil', 'class' => 'mt-3']) ?>

    <div class="mt-3 d-flex">
        <h1 class="d-inline-block"><?= $model->login?></h1>
        <div class="dropdown d-inline-block ml-auto my-auto">
            <?php if ($model->id == Yii::$app->user->id) : ?>
                <button class="dots-menu" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <?= Html::a('Editar perfil', ['usuarios/update', 'id' => $model->id], ['class' => 'dropdown-item']) ?>
                    <?= Html::a(
                        'Eliminar imagen de perfil',
                        ['usuarios/eliminar-imagen', 'id' => $model->id],
                        [
                            'class' => 'dropdown-item',
                            'data' => ['confirm' => Yii::t('app', 'Are you sure you want to delete this item?'), 'method' => 'post']
                        ]
                    ) ?>
                    <?= Html::a(
                        'Eliminar cuenta',
                        ['usuarios/eliminar-cuenta', 'id' => $model->id],
                        [
                            'class' => 'dropdown-item',
                            'data' => ['confirm' => Yii::t('app', 'Are you sure you want to delete this item?'), 'method' => 'post']
                        ]
                    ) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        <li class="nav-item ml-auto">
            <a class="nav-link active text-uppercase" id="canciones-tab" data-toggle="tab" href="#canciones" role="tab" aria-controls="canciones" aria-selected="true">Canciones</a>
        </li>
        <li class="nav-item mr-auto">
            <a class="nav-link text-uppercase" id="albumes-tab" data-toggle="tab" href="#albumes" role="tab" aria-controls="albumes" aria-selected="false">Albumes</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="canciones" role="tabpanel" aria-labelledby="canciones-tab">
            <div class="row">
                <?php if (count($canciones) > 0) : ?>
                    <?php foreach ($canciones as $cancion) : ?>
                        <div class="col-12 col-md-4 col-lg-3">
                            <button class="outline-transparent cancion" data-toggle="modal" data-target="#song-<?= $cancion->id ?>">
                                <?= Html::img($cancion->url_portada, ['class' => 'img-fluid'])?>
                            </button>
                            <div class="modal fade" id="song-<?= $cancion->id ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <div class="row">
                                                        <?= Html::img($cancion->url_portada, ['class' => 'img-fluid col-12']) ?>
                                                        <div class="col-12 mt-4">
                                                            <textarea class="form-control text-area-comment" cols="30" rows="3" placeholder="Comentario..."></textarea>
                                                            <div class="invalid-feedback">Debe tener como máximo 255 caracteres y no estar vacío.</div>
                                                            <button class="btn btn-sm main-yellow mt-2 comment-btn" id="comment-<?= $cancion->id ?>" type="button">Comentar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 custom-overflow">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div>
                                                                <button type="button" id="like-<?= $cancion->id ?>" class="btn-lg outline-transparent like-btn"><i class="fa-heart text-danger"></i></button>
                                                                <p><span></span> likes</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
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
                    <?php endforeach; ?>
                <?php else :?>
                    <div class="row mt-5 justify-content-center text-center">
                        <div class="col-12">
                            <h2>Parece que no ha subido ninguna canción aún.</h2>
                        </div>
                        <div class="col-10 col-lg-6">
                            <?= Html::img('@web/img/undraw_recording_lywr.png', ['class' => 'img-fluid']) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="tab-pane fade" id="albumes" role="tabpanel" aria-labelledby="albumes-tab">
            <div class="row">
                <?php if (count($albumes) > 0) : ?>
                    <?php foreach ($albumes as $album) : ?>
                        <div class="col-lg-3">
                            <h2><?= $album->titulo?></h2>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="row mt-5 justify-content-center text-center">
                        <div class="col-12">
                            <h2>Parece que no tiene ningún álbum aún.</h2>
                        </div>
                        <div class="col-10 col-lg-4">
                            <?= Html::img('@web/img/undraw_no_data_qbuo.png', ['class' => 'img-fluid']) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>


</div>
