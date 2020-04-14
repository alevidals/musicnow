<?php

use app\controllers\UsuariosController;
use app\services\Utility;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

\yii\web\YiiAsset::register($this);

$urlFollow = Url::to(['seguidores/follow', 'seguido_id' => $model->id]);
$urlGetFollowData = Url::to(['seguidores/get-data', 'seguido_id' => $model->id]);

$playSongCode = Utility::PLAY_SONG;
$likeCommentProfile = Utility::LIKE_COMMENT_PROFILE;

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

    $likeCommentProfile
    $playSongCode

EOT;

$this->registerJS($js);

?>

<div class="usuarios-view">

    <?= Html::img('@web/img/banner.png', ['class' => 'img-fluid', 'alt' => 'banner']) ?>

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
                        'confirm' => '¿Estás seguro? Si sigues al usuario dejaras de seguirlo.', 'method' => 'post'
                    ]
                ]
            ) ?>
        <?php endif; ?>
    <?php endif; ?>

    <div class="row text-white text-center mt-4">
        <div class="col">
            <h4><span id="publicaciones"><?= $model->getCanciones()->count() ?></span> <?= Yii::t('app', 'Posts') ?></h4>
        </div>
        <div class="col">
            <button class="outline-transparent" type="button" data-toggle="modal" data-target="#seguidores-list">
                <h4><span id="seguidores"></span> <?= Yii::t('app', 'Followers') ?></h4>
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
                                                    <p class="d-inline-block my-auto"><?= $usuario->login ?></p>
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
        <div class="col">
            <button class="outline-transparent" type="button" data-toggle="modal" data-target="#seguidos-list">
                <h4><span id="seguidos"></span> <?= Yii::t('app', 'Following') ?></h4>
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
                                                    <p class="d-inline-block my-auto"><?= $usuario->login ?></p>
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

    <?= Html::img($model->url_image, ['width' => '100px', 'id' => 'image-perfil', 'class' => 'mt-3 user-search-img', 'alt' => 'profile-image']) ?>

    <div class="mt-3 d-flex">
        <h1 class="d-inline-block"><?= $model->login?></h1>
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
                            'data' => ['confirm' => Yii::t('app', 'Are you sure you want to delete this item?'), 'method' => 'post']
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
                            'data' => ['confirm' => Yii::t('app', 'Are you sure you want to delete this item?'), 'method' => 'post']
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
            <li class="nav-item mr-auto">
                <a class="nav-link text-uppercase" id="albumes-tab" data-toggle="tab" href="#albumes" role="tab" aria-controls="albumes" aria-selected="false"><?= Yii::t('app', 'Albumes') ?></a>
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
                                <h5 class="text-center"><?= $cancion->titulo ?></h5>
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
                        <?php endforeach; ?>
                    <?php else :?>
                        <div class="row mt-5 justify-content-center text-center">
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
                                <h2><?= $album->titulo?></h2>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="row mt-5 justify-content-center text-center">
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
        </div>
    <?php endif; ?>



</div>
