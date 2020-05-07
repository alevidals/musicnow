<?php

/* @var $this yii\web\View */

use yii\bootstrap4\Html;

$counterMasEscuchadas = 1;
$counterMasLikes = 1;

?>
<div class="site-index">

    <h1 class="text-center my-3"><?= Yii::t('app', 'TrendsOf') ?> <?= ucfirst(Yii::$app->formatter->asDate(date('F'), 'MMMM')) ?></h1>

    <h3><?= Yii::t('app', 'MostListened') ?></h3>
    <div class="row mt-3">
        <?php foreach ($cancionesMasEscuchadas as $cancion) : ?>
            <div class="col-12 playlist-cancion mb-4 fall-animation" id="song-<?= $cancion->id ?>">
                <h6 class="d-inline-block"><?= $counterMasEscuchadas++; ?></h6>
                <?= Html::img($cancion->url_portada, ['class' => 'img-fluid ml-3', 'alt' => 'portada', 'width' => '50px']) ?>
                <button id="play-<?= $cancion->id ?>" class="action-btn play-btn outline-transparent"><i class="fas fa-play"></i></button>
                <button data-song="<?= $cancion->id ?>" class="action-btn outline-transparent add-btn"><i class="fas fa-music"></i></button>
                <button data-song="<?= $cancion->id ?>" data-user="<?= Yii::$app->user->id ?>" class="action-btn outline-transparent playlist-btn" data-toggle="modal" data-target="#playlist"><i class="fas fa-plus"></i></button>
                <div class="text-truncate d-inline-block">
                    <h5 class="ml-3 my-auto" ><?= Html::encode($cancion->titulo) ?></h5>
                </div>
                <?php if ($cancion->explicit) : ?>
                    <span class="ml-3 badge explicit-badge">EXPLICIT</span>
                <?php endif; ?>
                <span class="ml-3 float-right"><?= (new DateInterval($cancion->duracion))->format('%i:%S') ?></span>
            </div>
        <?php endforeach ?>
    </div>

    <h3><?= Yii::t('app', 'MostLikes') ?></h3>
    <div class="row mt-3">
        <?php foreach ($cancionesConMasLikes as $cancion) : ?>
            <div class="col-12 playlist-cancion mb-4 fall-animation" id="song-<?= $cancion->id ?>">
                <h6 class="d-inline-block"><?= $counterMasLikes++; ?></h6>
                <?= Html::img($cancion->url_portada, ['class' => 'img-fluid ml-3', 'alt' => 'portada', 'width' => '50px']) ?>
                <button id="play-<?= $cancion->id ?>" class="action-btn play-btn outline-transparent"><i class="fas fa-play"></i></button>
                <button data-song="<?= $cancion->id ?>" class="action-btn outline-transparent add-btn"><i class="fas fa-music"></i></button>
                <button data-song="<?= $cancion->id ?>" data-user="<?= Yii::$app->user->id ?>" class="action-btn outline-transparent playlist-btn" data-toggle="modal" data-target="#playlist"><i class="fas fa-plus"></i></button>
                <div class="text-truncate d-inline-block">
                    <h5 class="ml-3 my-auto" ><?= Html::encode($cancion->titulo) ?></h5>
                </div>
                <?php if ($cancion->explicit) : ?>
                    <span class="ml-3 badge explicit-badge">EXPLICIT</span>
                <?php endif; ?>
                <span class="ml-3 float-right"><?= (new DateInterval($cancion->duracion))->format('%i:%S') ?></span>
            </div>
        <?php endforeach ?>
    </div>

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
