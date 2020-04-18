<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CancionesPlaylist */

$this->title = $model->playlist_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Canciones Playlists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="canciones-playlist-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'playlist_id' => $model->playlist_id, 'cancion_id' => $model->cancion_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'playlist_id' => $model->playlist_id, 'cancion_id' => $model->cancion_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'playlist_id',
            'cancion_id',
        ],
    ]) ?>

</div>
