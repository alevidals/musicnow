<?php

use yii\bootstrap4\Html;

?>
<div class="row">
        <?php foreach ($playlists as $playlist) : ?>
            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="song-container">
                    <div class="box-3">
                        <?php if ($playlist->getCanciones()->count() > 0) : ?>
                            <?= Html::img($playlist->getCanciones()->one()->url_portada, ['class' => 'img-fluid', 'alt' => 'portada'])?>
                        <?php else : ?>
                            <?= Html::img('@web/img/playlists.png', ['class' => 'img-fluid', 'alt' => 'portada'])?>
                        <?php endif; ?>
                        <div class="share-buttons">
                            <button id="<?= $playlist->id ?>" class="action-btn play-playlist-btn outline-transparent"><i class="fas fa-play"></i></button>
                            <?= Html::a(
                                '<i class="fas fa-eye"></i>',
                                ['playlists/view', 'id' => $playlist->id],
                                ['class' => 'action-btn outline-transparent', 'rol' => 'button']
                            ) ?>
                            <?= Html::a(
                                '<i class="fas fa-pen"></i>',
                                ['playlists/update', 'id' => $playlist->id],
                                ['class' => 'action-btn outline-transparent', 'rol' => 'button']
                            ) ?>
                            <?= Html::a(
                                '<i class="fas fa-trash"></i>',
                                ['playlists/delete', 'id' => $playlist->id],
                                ['class' => 'action-btn outline-transparent', 'rol' => 'button',  'data' => ['confirm' => Yii::t('app', 'Are you sure you want to delete this item?'), 'method' => 'post']],
                            ) ?>
                        </div>
                        <div class="layer"></div>
                    </div>
                </div>
                <h5 class="text-center my-3"><?= Html::encode($playlist->titulo) ?></h5>
            </div>
        <?php endforeach; ?>
    </div>
