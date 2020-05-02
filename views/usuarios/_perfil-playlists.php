<?php

use yii\bootstrap4\Html;

?>

<div class="tab-pane fade" id="playlists" role="tabpanel" aria-labelledby="playlists-tab">
    <div class="row">
        <?php if (count($playlists) > 0) : ?>
            <?php foreach ($playlists as $playlist) : ?>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3" itemscope itemtype="https://schema.org/MusicPlaylist">
                    <div class="song-container">
                        <div class="box-3">
                            <?= Html::img($playlist->getCanciones()->one()->url_portada, ['class' => 'img-fluid', 'alt' => 'portada', 'itemprop' => 'image'])?>
                            <div class="share-buttons">
                                <button id="<?= $playlist->id ?>" class="action-btn play-playlist-btn outline-transparent"><i class="fas fa-play"></i></button>
                                <?php if ($model->id != Yii::$app->user->id) : ?>
                                    <button id="<?= $playlist->id ?>" class="action-btn copy-playlist-btn outline-transparent"><i class="fas fa-copy"></i></button>
                                <?php endif; ?>
                                <?= Html::a(
                                    '<i class="fas fa-eye"></i>',
                                    ['playlists/view', 'id' => $playlist->id],
                                    ['class' => 'action-btn outline-transparent', 'rol' => 'button']
                                ) ?>
                            </div>
                            <div class="layer"></div>
                        </div>
                    </div>
                    <h5 class="text-center my-3" itemprop="name"><?= Html::encode($playlist->titulo) ?></h5>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="row mt-5 justify-content-center text-center mx-0">
                <div class="col-12">
                    <h2><?= Yii::t('app', 'NoPlaylists') ?></h2>
                </div>
                <div class="col-10 col-lg-4">
                    <?= Html::img('@web/img/undraw_playlist_5e13.png', ['class' => 'img-fluid', 'alt' => 'girl-music']) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>