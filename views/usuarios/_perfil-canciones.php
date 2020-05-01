<?php

use yii\bootstrap4\Html;

?>

<div class="tab-pane fade show active" id="canciones" role="tabpanel" aria-labelledby="canciones-tab">
    <div class="row">
        <?php if (count($canciones) > 0) : ?>
            <?php foreach ($canciones as $cancion) : ?>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="song-container">
                        <div class="box-3">
                            <?= Html::img($cancion->url_portada, ['class' => 'img-fluid', 'alt' => 'portada'])?>
                            <div class="share-buttons">
                                <button id="play-<?= $cancion->id ?>" class="action-btn play-btn outline-transparent"><i class="fas fa-play"></i></button>
                                <button id="outerlike-<?= $cancion->id ?>" class="action-btn outline-transparent like-btn"><i class="<?= in_array($cancion->id, $likes) ? 'fas' : 'far' ?> fa-heart red-hearth"></i></button>
                                <button class="action-btn outline-transparent cancion" data-toggle="modal" data-target="#song-<?= $cancion->id ?>"><i class="far fa-comment"></i></button>
                                <button data-song="<?= $cancion->id ?>" class="action-btn outline-transparent add-btn"><i class="fas fa-music"></i></button>
                                <button data-song="<?= $cancion->id ?>" data-user="<?= Yii::$app->user->id ?>" class="action-btn outline-transparent playlist-btn" data-toggle="modal" data-target="#playlist"><i class="fas fa-plus"></i></button>
                            </div>
                            <div class="layer"></div>
                        </div>
                    </div>
                    <h5 class="text-center my-3"><?= Html::encode($cancion->titulo) ?></h5>
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