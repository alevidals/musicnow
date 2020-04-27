<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CancionesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Canciones');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="canciones-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Canciones'), ['create'], ['class' => 'btn main-yellow', 'data-pjax' => 0]) ?>
    </p>

    <?php echo $this->render('_search', ['model' => $searchModel]);?>

    <div class="mt-3"></div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => '',
                'value' => function ($model, $key, $index, $column) {
                    return Html::img($model->url_portada, ['width' => '47px', 'alt' => 'portada']);
                },
                'format' => 'raw',
            ],
            'titulo',
            [
                'attribute' => 'album.titulo',
                'label' => Yii::t('app', 'Título del álbum'),
            ],
            [
                'attribute' => 'genero.denominacion',
                'label' => Yii::t('app', 'Género'),
            ],
            'anyo',
            'usuario.login',
            [
                'attribute' => 'explicit',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->explicit) {
                        return '<span class="badge explicit-badge">EXPLICIT</span>';
                    } else {
                        return 'No';
                    }
                },
                'format' => 'raw',
            ],
            // 'duracion',
            //'song_name',
            //'image_name',
            // 'created_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Actions'),
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-eye"></i>', [
                            'canciones/view', 'id' => $model->id,
                        ], [
                            'class' => 'btn btn-sm p-0 pr-1 shadow-none',
                        ]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-pen"></i>', [
                            'canciones/update', 'id' => $model->id,
                        ], [
                            'class' => 'btn btn-sm p-0 shadow-none',
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-trash"></i>', [
                            'canciones/delete', 'id' => $model->id,
                        ], [
                            'id' => $model->id,
                            'class' => 'btn btn-sm p-0 pl-1 shadow-none',
                            'data' => ['confirm' => Yii::t('app', 'Are you sure you want to delete this item?'), 'method' => 'post']
                        ]);
                    },
                ],
            ],
        ],
        'tableOptions' => [
            'class' => 'table admin-table ',
        ],
    ]); ?>


</div>
