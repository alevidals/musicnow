<?php

use app\controllers\UsuariosController;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

\yii\web\YiiAsset::register($this);

$urlFollow = Url::to(['seguidores/follow', 'seguido_id' => $model->id]);
$urlGetFollowData = Url::to(['seguidores/get-data', 'seguido_id' => $model->id]);
$urlGetLikes = Url::to(['canciones/get-likes']);

$confirmMessage = Yii::t('app', 'Are you sure you want to delete this item?');

$js = <<<EOT

    $.ajax({
        'method': 'GET',
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

    $('.like-list').on('click', function ev(e) {
        var cancion_id = $(this).data('song');
        $.ajax({
            method: 'GET',
            'url': '$urlGetLikes&cancion_id=' + cancion_id,
            success: function (data) {
                $('.like-row').html('');
                data.forEach(element => {
                    $('.like-row').append(`
                        <div class="col-12">
                                <img src="\${element.url_image}" class="d-inline-block user-search-img my-auto" width="30px" alt="like">
                                <p class="d-inline-block my-auto">\${element.login}</p>
                        </div>
                    `);
                });
            }
        });
    });

    $('body').on('click', '.remove-videoclip-btn', function ev(e) {
    var id = $(this).data('id');
    var accept = confirm('$confirmMessage');
    if (accept) {
        $.ajax({
            method: 'POST',
            url: '/index.php?r=videoclips%2Feliminar',
            data: {
                id: id
            },
            success: function (data) {
                $('#video-' + data).addClass('fall');
                $('#video-' + data).on('transitionend', function ev(e) {
                    $('#video-' + data).remove();
                });
            }
        });
    }
});

EOT;

$this->registerJS($js);

?>

<div class="usuarios-view">

    <?php if ($model->url_banner) : ?>
        <?= Html::img($model->url_banner, ['class' => 'img-fluid', 'alt' => 'banner']) ?>
    <?php endif; ?>


    <?php if ($model->id != Yii::$app->user->id) : ?>
        <?php if ($bloqueo != UsuariosController::OTHER_BLOCK
            and $bloqueo != UsuariosController::YOU_BLOCK) : ?>
            <button class="btn main-yellow mt-4 follow"></button>
            <?= Html::a(
                Yii::t('app', 'Block'),
                ['bloqueados/bloquear', 'bloqueado_id' => $model->id],
                [
                    'role' => 'button',
                    'class' => 'btn main-yellow mt-4',
                    'data' => [
                        'confirm' => '¿Estás seguro? Si sigues al usuario dejaras de seguirlo.', 'method' => 'post',
                    ],
                ]
            ) ?>
        <?php endif; ?>
    <?php endif; ?>


    <div class="d-flex mt-5">
        <div>
            <?= Html::img($model->url_image, ['width' => '90px', 'id' => 'image-perfil', 'class' => 'user-search-img mr-3', 'alt' => 'profile-image']) ?>
        </div>
        <div class="w-100">
            <div class="text-white text-center mt-4 d-flex justify-content-around">
                <div>
                    <span id="publicaciones" class="font-weight-bold"><?= $model->getCanciones()->count() ?></span>
                    <p><?= Yii::t('app', 'Posts') ?></p>
                </div>
                <div>
                    <button class="outline-transparent" type="button" data-toggle="modal" data-target="#seguidores-list">
                        <div>
                            <span id="seguidores" class="font-weight-bold"></span>
                            <p><?= Yii::t('app', 'Followers') ?></p>
                        </div>
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
                                                            <?= Html::img($model->url_image, ['class' => 'd-inline-block user-search-img my-auto', 'width' => '30px', 'alt' => 'seguidor']) ?>
                                                        </div>
                                                        <div class="col">
                                                            <p class="d-inline-block my-auto"><?= Html::encode($usuario->login) ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <div class="col-12">
                                                <p class="my-auto"><?= Yii::t('app', 'NoFollowYou') ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <button class="outline-transparent" type="button" data-toggle="modal" data-target="#seguidos-list">
                        <div>
                            <span id="seguidos" class="font-weight-bold"></span>
                            <p><?= Yii::t('app', 'Following') ?></p>
                        </div>
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
                                                            <?= Html::img($model->url_image, ['class' => 'd-inline-block user-search-img my-auto', 'width' => '30px', 'alt' => 'seguidos']) ?>
                                                        </div>
                                                        <div class="col">
                                                            <p class="d-inline-block my-auto"><?= Html::encode($usuario->login) ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <div class="col-12">
                                                <p class="my-auto"><?= Yii::t('app', 'YouDoNotFollow') ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="mt-3 d-flex">
        <h1 class="d-inline-block"><?= Html::encode($model->login) ?></h1>
        <div class="dropdown d-inline-block ml-auto my-auto">
            <?php if ($model->id == Yii::$app->user->id) : ?>
                <button class="dots-menu" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <?= Html::a(Yii::t('app', 'ProfileEdit'), ['usuarios/update', 'id' => $model->id], ['class' => 'dropdown-item']) ?>
                    <?= Html::a(
                Yii::t('app', 'DeleteProfileImage'),
                ['usuarios/eliminar-imagen', 'id' => $model->id],
                [
                            'class' => 'dropdown-item',
                            'data' => ['confirm' => Yii::t('app', 'Are you sure you want to delete this item?'), 'method' => 'post'],
                        ]
            ) ?>
                    <?= Html::a(
                Yii::t('app', 'DeleteProfileBanner'),
                ['usuarios/eliminar-banner', 'id' => $model->id],
                [
                            'class' => 'dropdown-item',
                            'data' => ['confirm' => Yii::t('app', 'Are you sure you want to delete this item?'), 'method' => 'post'],
                        ]
            ) ?>
                    <?= Html::a(
                        Yii::t('app', 'DeleteComments'),
                        ['comentarios/index', 'user_id' => $model->id],
                        [
                            'class' => 'dropdown-item',
                        ]
                    ) ?>
                    <?= Html::a(
                        Yii::t('app', 'DeleteAccount'),
                        ['usuarios/eliminar-cuenta', 'id' => $model->id],
                        [
                            'class' => 'dropdown-item',
                            'data' => ['confirm' => Yii::t('app', 'Are you sure you want to delete this item?'), 'method' => 'post'],
                        ]
                    ) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($bloqueo == UsuariosController::OTHER_BLOCK) : ?>
        <h3><?= Yii::t('app', 'OtherBlock') ?></>
    <?php elseif ($bloqueo == UsuariosController::YOU_BLOCK) : ?>
        <h3><?= Yii::t('app', 'YouBlock') ?></h3>
        <?= Html::a('Desbloquear', ['bloqueados/bloquear', 'bloqueado_id' => $model->id], ['role' => 'button', 'class' => 'btn main-yellow']) ?>
    <?php else : ?>
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item ml-auto">
                <a class="nav-link active text-uppercase" id="canciones-tab" data-toggle="tab" href="#canciones" role="tab" aria-controls="canciones" aria-selected="true"><?= Yii::t('app', 'Canciones') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="albumes-tab" data-toggle="tab" href="#albumes" role="tab" aria-controls="albumes" aria-selected="false"><?= Yii::t('app', 'Albumes') ?></a>
            </li>
            <li class="nav-item mr-auto">
                <a class="nav-link text-uppercase" id="videoclips-tab" data-toggle="tab" href="#videoclips" role="tab" aria-controls="videoclips" aria-selected="false">Videoclips</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="canciones" role="tabpanel" aria-labelledby="canciones-tab">
                <div class="row">
                    <?php if (count($canciones) > 0) : ?>
                        <?php foreach ($canciones as $cancion) : ?>
                            <div class="col-12 col-md-4 col-lg-3">
                                <div class="song-container">
                                    <div class="box-3">
                                        <?= Html::img($cancion->url_portada, ['class' => 'img-fluid', 'alt' => 'portada'])?>
                                        <div class="share-buttons">
                                            <button id="play-<?= $cancion->id ?>" class="action-btn play-btn outline-transparent"><i class="fas fa-play"></i></button>
                                            <button id="outerlike-<?= $cancion->id ?>" class="action-btn outline-transparent like-btn"><i class="<?= in_array($cancion->id, $likes) ? 'fas' : 'far' ?> fa-heart"></i></button>
                                            <button class="action-btn outline-transparent cancion" data-toggle="modal" data-target="#song-<?= $cancion->id ?>"><i class="far fa-comment"></i></button>
                                        </div>
                                        <div class="layer"></div>
                                    </div>
                                </div>
                                <h5 class="text-center"><?= Html::encode($cancion->titulo) ?></h5>
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
                                                                    <p class="d-inline-block">
                                                                        <span></span>
                                                                        <button class="outline-transparent like-list" data-song="<?= $cancion->id ?>" type="button" data-toggle="modal" data-target="#likes-list">
                                                                            like/s
                                                                        </button>
                                                                    </p>
                                                                    <div class="modal fade" id="likes-list" tabindex="-1" role="dialog" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-body">
                                                                                    <h4>Likes</h4>
                                                                                    <div class="like-row">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
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
                        <?php endforeach; ?>
                    <?php else :?>
                        <div class="row mt-5 justify-content-center text-center mx-0">
                            <div class="col-12">
                                <h2><?= Yii::t('app', 'NoSongs') ?></h2>
                            </div>
                            <div class="col-10 col-lg-6">
                                <?= Html::img('@web/img/undraw_recording_lywr.png', ['class' => 'img-fluid', 'alt' => 'girl-music']) ?>
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
                                <h2><?= Html::encode($album->titulo) ?></h2>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="row justify-content-center text-center mx-0">
                            <div class="col-12">
                                <h2><?= Yii::t('app', 'NoAlbums') ?></h2>
                            </div>
                            <div class="col-10 col-lg-4">
                                <?= Html::img('@web/img/undraw_no_data_qbuo.png', ['class' => 'img-fluid', 'alt' => 'girl-music']) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tab-pane fade" id="videoclips" role="tabpanel" aria-labelledby="videoclips-tab">
                <?php if ($model->id == Yii::$app->user->id) : ?>
                    <button class="action-btn outline-transparent mb-4" data-toggle="modal" data-target="#videoclip-modal"><i class="far fa-plus-square"></i></button>
                    <div class="modal fade" id="videoclip-modal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <form id="add-videoclip-form">
                                        <div class="form-group">
                                            <label for="link">Youtube link</label>
                                            <input class="form-control" type="text" name="link" id="link" placeholder="https://www.youtube.com/watch?v=KHAgoT4FZbc">
                                        </div>
                                        <button class="btn main-yellow add-videoclip-btn" type="submit">Enviar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (count($videoclips) > 0) : ?>
                    <div class="row row-videoclips">
                        <?php foreach ($videoclips as $videoclip) : ?>
                            <div id="video-<?= $videoclip->id ?>" class="col-12 col-lg-6 mb-4 fall-animation">
                                <?php if ($model->id == Yii::$app->user->id) : ?>
                                    <button data-id="<?= $videoclip->id ?>" class="action-btn remove-videoclip-btn outline-transparent mb-4"><i class="fas fa-trash"></i></button>
                                <?php endif; ?>
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe class="embed-responsive-item" src="<?= $videoclip->link ?>" allowfullscreen></iframe>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="row mt-5 justify-content-center text-center mx-0 videoclip-warning">
                        <div class="col-12">
                            <h2><?= Yii::t('app', 'NoVideoclips') ?></h2>
                        </div>
                        <div class="col-10 col-lg-4 mt-4">
                            <?= Html::img('@web/img/undraw_video_influencer_9oyy.png', ['class' => 'img-fluid', 'alt' => 'girl-music']) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>



</div>
