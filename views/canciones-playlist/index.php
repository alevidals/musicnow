<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CancionesPlaylistSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Canciones-Playlists');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="canciones-playlist-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="mt-3"></div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'playlist.titulo',
                'label' => 'Playlist'
            ],
            [
                'attribute' => 'cancion.titulo',
                'label' => Yii::t('app', 'Cancion')
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
            'tableOptions' => [
            'class' => 'table admin-table ',
        ],
    ]); ?>


</div>
