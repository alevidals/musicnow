<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AlbumesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Albumes');
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    [
        'label' => '',
        'value' => function ($model, $key, $index, $column) {
            return Html::img($model->url_portada, ['width' => '47px', 'alt' => 'portada']);
        },
        'format' => 'raw',
    ],
    'titulo',
    'anyo',
    [
        'attribute' => 'total',
        'label' => Yii::t('app', 'Canciones')
    ],
    'created_at:datetime',
    [
        'class' => 'yii\grid\ActionColumn',
        'header' => Yii::t('app', 'Actions'),
        'template' => '{view} {update} {delete}',
        'buttons' => [
            'view' => function ($url, $model, $key) {
                return Html::a('<i class="fas fa-eye"></i>', [
                    'albumes/view', 'id' => $model->id,
                ], [
                    'class' => 'btn btn-sm p-0 pr-1 shadow-none',
                ]);
            },
            'update' => function ($url, $model, $key) {
                return Html::a('<i class="fas fa-pen"></i>', [
                    'albumes/update', 'id' => $model->id,
                ], [
                    'class' => 'btn btn-sm p-0 shadow-none',
                ]);
            },
            'delete' => function ($url, $model, $key) {
                return Html::a('<i class="fas fa-trash"></i>', [
                    'albumes/delete', 'id' => $model->id,
                ], [
                    'id' => $model->id,
                    'class' => 'btn btn-sm p-0 pl-1 shadow-none',
                    'data' => ['confirm' => Yii::t('app', 'Are you sure you want to delete this item?'), 'method' => 'post']
                ]);
            },
        ],
    ],
];

if (Yii::$app->user->identity->rol_id == 1) {
    $columns = array_merge([[
        'attribute' => 'usuario.login',
        'label' => 'Login',
    ]], $columns);
}

?>
<div class="albumes-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Albumes'), ['create'], ['class' => 'btn main-yellow', 'data-pjax' => 0]) ?>
    </p>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="mt-3"></div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => $columns,
        'tableOptions' => [
            'class' => 'table admin-table '
        ]
    ]); ?>


</div>
