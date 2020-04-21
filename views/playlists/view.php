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

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn main-yellow']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'metho
                d' => 'post',
            ],
        ]) ?>
    </p>

    <h2 class="text-center"><?= Html::encode($model->titulo) ?></h2>

    <?php if (count($canciones) > 0) : ?>
        <button id="<?= $model->id ?>" class="outline-transparent action-btn play-playlist-btn">
            <i class="fas fa-play"></i>
        </button>
    <?php else : ?>
        <p><?= Yii::t('app', 'NoSongs') ?></p>
    <?php endif; ?>

    <div class="row mt-3">
        <?php foreach ($canciones as $cancion) : ?>
            <div class="col-12 playlist-cancion mb-3">
                <h5 class="d-inline-block"><?= $counter++; ?></h5>
                <?= Html::img($cancion->url_portada, ['class' => 'img-fluid ml-3', 'alt' => 'portada', 'width' => '50px']) ?>
                <h5 class="d-inline-block ml-3"><?= Html::encode($cancion->titulo) ?></h5>
            </div>
        <?php endforeach ?>
    </div>
</div>
