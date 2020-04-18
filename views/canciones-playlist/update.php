<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CancionesPlaylist */

$this->title = Yii::t('app', 'Update Canciones Playlist: {name}', [
    'name' => $model->playlist_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Canciones Playlists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->playlist_id, 'url' => ['view', 'playlist_id' => $model->playlist_id, 'cancion_id' => $model->cancion_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="canciones-playlist-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
