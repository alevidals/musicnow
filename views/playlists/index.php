<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PlaylistsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Playlists');
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    'titulo',
    [
        'class' => 'yii\grid\ActionColumn',
        'header' => Yii::t('app', 'Actions'),
        'template' => '{view} {update} {delete}',
        'buttons' => [
            'view' => function ($url, $model, $key) {
                return Html::a('<i class="fas fa-eye"></i>', [
                    'playlists/view', 'id' => $model->id,
                ], [
                    'class' => 'btn btn-sm p-0 pr-1 shadow-none',
                ]);
            },
            'update' => function ($url, $model, $key) {
                return Html::a('<i class="fas fa-pen"></i>', [
                    'playlists/update', 'id' => $model->id,
                ], [
                    'class' => 'btn btn-sm p-0 shadow-none',
                ]);
            },
            'delete' => function ($url, $model, $key) {
                return Html::a('<i class="fas fa-trash"></i>', [
                    'playlists/delete', 'id' => $model->id,
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
        'attribute' => 'usuario.login'
    ]], $columns);
}

?>
<div class="playlists-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Playlists'), ['create'], ['class' => 'btn main-yellow', 'data-pjax' => 0]) ?>
    </p>

    <?php if (Yii::$app->user->identity->rol_id == 1) : ?>
        <?= $this->render('_admins-playlist-view', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'columns' => $columns,
        ]) ?>
    <?php else : ?>
        <?= $this->render('_users-playlist-view', [
            'playlists' => $playlists,
        ]) ?>
    <?php endif;?>


</div>
