<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Albumes */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Albumes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);



$counter = 1;

?>
<div class="albumes-view" itemscope itemtype="https://schema.org/MusicAlbum">

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn main-yellow']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="text-center">
        <?= Html::img($model->url_portada, ['width' => '300px', 'itemprop' => 'image']) ?>
        <h2 class="mt-3" itemprop="name"><?= Html::encode($model->titulo) ?></h2>
        <h6><meta itemprop="timeRequired" content="<?= $duration ?>"><?= Yii::$app->formatter->asDuration($duration) ?></h6>
    </div>

    <?php if (count($canciones) > 0) : ?>
        <button id="<?= $model->id ?>" class="outline-transparent action-btn play-album-btn">
            <i class="fas fa-play"></i>
        </button>
    <?php else : ?>
        <p><?= Yii::t('app', 'NoSongs') ?></p>
    <?php endif; ?>

    <div class="row mt-3">
        <?php foreach ($canciones as $cancion) : ?>
            <div class="col-12 playlist-cancion mb-4 fall-animation" id="song-<?= $cancion->id ?>" itemprop="track" itemscope itemtype="https://schema.org/MusicRecording">
                <h6 class="d-inline-block"><?= $counter++; ?></h6>
                <?= Html::img($cancion->url_portada, ['class' => 'img-fluid ml-3', 'alt' => 'portada', 'width' => '50px', 'itemprop' => 'image']) ?>
                <button data-song="<?= $cancion->id ?>" class="action-btn outline-transparent add-btn"><i class="fas fa-music"></i></button>
                <button data-song="<?= $cancion->id ?>" data-user="<?= Yii::$app->user->id ?>" class="action-btn outline-transparent playlist-btn" data-toggle="modal" data-target="#playlist"><i class="fas fa-plus"></i></button>
                <div class="text-truncate d-inline-block">
                    <h5 class="ml-3 my-auto" itemprop="name"><?= Html::encode($cancion->titulo) ?></h5>
                </div>
                <?php if ($cancion->explicit) : ?>
                    <span class="ml-3 badge explicit-badge">EXPLICIT</span>
                <?php endif; ?>
                <span class="ml-3 float-right"> <meta itemprop="duration" content="<?= $cancion->duracion ?>"><?= (new DateInterval($cancion->duracion))->format('%i:%S') ?></span>
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
