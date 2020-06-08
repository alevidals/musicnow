<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LikesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Likes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="likes-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="mt-3"></div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            'usuario.login',
            [
                'attribute' => 'cancion.titulo',
                'label' => Yii::t('app', 'Cancion')
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Actions'),
                'template' => '{view} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-eye"></i>', [
                            'likes/view', 'usuario_id' => $model->usuario_id, 'cancion_id' => $model->cancion_id,
                        ], [
                            'class' => 'btn btn-sm p-0 pr-1 shadow-none',
                        ]);
                    },
                    // 'update' => function ($url, $model, $key) {
                    //     return Html::a('<i class="fas fa-pen"></i>', [
                    //         'likes/update', 'usuario_id' => $model->usuario_id, 'cancion_id' => $model->cancion_id,
                    //     ], [
                    //         'class' => 'btn btn-sm p-0 shadow-none',
                    //     ]);
                    // },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-trash"></i>', [
                            'likes/delete', 'usuario_id' => $model->usuario_id, 'cancion_id' => $model->cancion_id,
                        ], [
                            'usuario_id' => $model->usuario_id, 'cancion_id' => $model->cancion_id,
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
