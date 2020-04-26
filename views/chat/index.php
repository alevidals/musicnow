<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Chats');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chat-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Chat'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="mt-3"></div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'emisor.login',
                'label' => Yii::t('app', 'Emisor'),
            ],
            [
                'attribute' => 'receptor.login',
                'label' => Yii::t('app', 'Receptor'),
            ],
            'mensaje:text',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Actions'),
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-eye"></i>', [
                            'chat/view', 'id' => $model->id,
                        ], [
                            'class' => 'btn btn-sm p-0 pr-1 shadow-none',
                        ]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-pen"></i>', [
                            'chat/update', 'id' => $model->id,
                        ], [
                            'class' => 'btn btn-sm p-0 shadow-none',
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-trash"></i>', [
                            'chat/delete', 'id' => $model->id,
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
