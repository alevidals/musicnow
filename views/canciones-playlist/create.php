<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CancionesPlaylist */

$this->title = Yii::t('app', 'Create Canciones Playlist');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Canciones Playlists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="canciones-playlist-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
