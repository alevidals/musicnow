<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ComentariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Comments');
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    [
        'attribute' => 'cancion.titulo',
        'label' => Yii::t('app', 'Cancion'),
    ],
    [
        'attribute' => 'comentario',
        'label' => Yii::t('app', 'Comment'),
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'header' => Yii::t('app', 'Actions'),
        'template' => '{view} {update} {delete}',
        'buttons' => [
            'view' => function ($url, $model, $key) {
                return Html::a('<i class="fas fa-eye"></i>', [
                    'comentarios/view', 'id' => $model->id,
                ], [
                    'class' => 'btn btn-sm p-0 pr-1 shadow-none',
                ]);
            },
            'update' => function ($url, $model, $key) {
                return Html::a('<i class="fas fa-pen"></i>', [
                    'comentarios/update', 'id' => $model->id,
                ], [
                    'class' => 'btn btn-sm p-0 shadow-none',
                ]);
            },
            'delete' => function ($url, $model, $key) {
                return Html::a('<i class="fas fa-trash"></i>', [
                    'comentarios/delete', 'id' => $model->id,
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
<div class="comentarios-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]);?>

    <div class="mt-3"></div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => $columns,
        'tableOptions' => [
            'class' => 'table admin-table ',
        ],
    ]); ?>


</div>
