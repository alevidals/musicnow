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
                'value' => function ($model, $key, $index, $column) {
                        return Html::img($model->url_portada, ['width' => '47px']);
                },
                'format' => 'raw',
            ],
            [
                'label' => '',
                'value' => function ($model, $key, $index, $column) {
                        return <<<EOT
                                <audio controls>
                                    <source src="$model->url_cancion">
                                </audio>
                        EOT;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'titulo',
                'value' => function ($model, $key, $index, $column) {
                    return substr($model->titulo, 0, 23) . '...';
                }
            ],
            [
                'attribute' => 'album.titulo',
                'label' => Yii::t('app', 'Título del álbum')
            ],
            [
                'attribute' => 'genero.denominacion',
                'label' => Yii::t('app', 'Género')
            ],
            'anyo',
            // 'duracion',
            // 'created_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {portada}',
                'buttons' => [
                    'portada' => function ($url, $model, $key) {
                        return Html::a('Portada', ['canciones/imagen', 'id' => $model->id]);
                    }
                ],
            ],
        ],
        'tableOptions' => [
            'class' => 'table admin-table '
        ],
    ]); ?>


</div>
