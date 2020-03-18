<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CancionesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Canciones');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="canciones-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Canciones'), ['create'], ['class' => 'btn main-yellow']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => '',
                'attribute' => 'imagen',
                'value' => function ($model, $key, $index, $column) {
                        return Html::img($model->url_portada, ['class' => 'img-fluid', 'width' => 100]);
                },
                'format' => 'raw',
            ],
            'titulo',
            [
                'attribute' => 'album.titulo',
                'label' => Yii::t('app', 'Título del álbum')
            ],
            [
                'attribute' => 'genero.denominacion',
                'label' => Yii::t('app', 'Género')
            ],
            'url_cancion:url',
            'anyo',
            'duracion',
            // 'created_at:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
        'tableOptions' => [
            'class' => 'table admin-table '
        ],
    ]); ?>


</div>
