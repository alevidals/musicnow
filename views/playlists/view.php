<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Playlists */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Playlists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$counter = 1;

\yii\web\YiiAsset::register($this);
?>
<div class="playlists-view">

    <span class="d-none playlist-id"><?= $model->id ?></span>

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

    <h2><?= Html::encode($model->titulo) ?></h2>
    <h6><?= Yii::$app->formatter->asDuration($duration) ?></h6>

    <?php if (count($canciones) > 0) : ?>
        <button id="<?= $model->id ?>" class="outline-transparent action-btn play-playlist-btn">
            <i class="fas fa-play"></i>
        </button>
    <?php else : ?>
        <p><?= Yii::t('app', 'NoSongs') ?></p>
    <?php endif; ?>

    <div class="row mt-3">
        <?php foreach ($canciones as $cancion) : ?>
            <div class="col-12 playlist-cancion mb-4 fall-animation" id="song-<?= $cancion->id ?>">
                <h6 class="d-inline-block"><?= $counter++; ?></h6>
                <?= Html::img($cancion->url_portada, ['class' => 'img-fluid ml-3', 'alt' => 'portada', 'width' => '50px']) ?>
                <div class="text-truncate d-inline-block">
                    <h5 class="ml-3 my-auto"><?= Html::encode($cancion->titulo) ?></h5>
                </div>
                <?php if ($cancion->explicit) : ?>
                    <span class="ml-3 badge explicit-badge">EXPLICIT</span>
                <?php endif; ?>
                <div class="float-right">
                    <span class="ml-3"><?= (new DateInterval($cancion->duracion))->format('%i:%S') ?></span>
                    <button class="outline-transparent delete-song-playlist-btn" data-song-id="<?= $cancion->id ?>">
                        <i class="fas fa-trash text-danger"></i>
                    </button>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>
