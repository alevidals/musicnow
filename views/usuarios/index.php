<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UsuariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Usuarios');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuarios-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Usuarios'), ['create'], ['class' => 'btn main-yellow']) ?>
    </p>

    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="mt-3"></div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'login',
            'nombre',
            'apellidos',
            'email:email',
            'rol.rol',
            //'password',
            //'url_image:url',
            //'image_name',
            //'fnac',
            //'auth_key',
            //'confirm_token',
            //'created_at',
            //'deleted_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Actions'),
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-eye"></i>', [
                            'usuarios/view', 'id' => $model->id,
                        ], [
                            'class' => 'btn btn-sm p-0 pr-1 shadow-none',
                        ]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-pen"></i>', [
                            'usuarios/update', 'id' => $model->id,
                        ], [
                            'class' => 'btn btn-sm p-0 shadow-none',
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-trash"></i>', [
                            'usuarios/delete', 'id' => $model->id,
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
            'class' => 'table admin-table '
        ],
    ]); ?>


</div>
