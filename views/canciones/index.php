<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CancionesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Canciones');
$this->params['breadcrumbs'][] = $this->title;

$apiKey = getenv('apiKey');
$authDomain = getenv('authDomain');
$databaseURL = getenv('databaseURL');
$projectId = getenv('projectId');
$storageBucket = getenv('storageBucket');
$messagingSenderId = getenv('messagingSenderId');
$appId = getenv('appId');

$url = Url::to(['canciones/url']);
$urlBorrar = Url::to(['canciones/delete']);
$msg = Yii::t('app', 'Are you sure you want to delete this item?');
$js = <<<EOT

    var firebaseConfig = {
        apiKey: "$apiKey",
        authDomain: "$authDomain",
        databaseURL: "$databaseURL",
        projectId: "$projectId",
        storageBucket: "$storageBucket",
        messagingSenderId: "$messagingSenderId",
        appId: "$appId",
    };

    firebase.initializeApp(firebaseConfig);

    $('.delete').on('click', function(ev) {
        var id = $(this).attr('id');
        var confirmar = confirm('$msg');
        if (confirmar) {
            $.ajax({
                method: 'GET',
                url: '$url',
                data: {
                    id: id
                },
                success: function (data, code, jqXHR) {
                    var storage = firebase.storage();
                    var storageRef = storage.ref();
                    var songRef = storageRef.child('temas/' + data.usuario_id + '/' + data.file_name);
                    songRef.delete().then(function() {
                        $.ajax({
                            method: 'POST',
                            url: '$urlBorrar&id=' + id,
                            success: function (data, code, jqXHR) {
                                console.log(data);
                            }
                        });
                    });
                }
            });
        }
    });
EOT;


$this->registerJsFile('@web/js/firebase-app.js');
$this->registerJsFile('@web/js/firebase-storage.js');
$this->registerJS($js);


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
            'titulo',
            [
                'attribute' => 'album.titulo',
                'label' => Yii::t('app', 'Título del álbum')
            ],
            [
                'attribute' => 'genero.denominacion',
                'label' => Yii::t('app', 'Género')
            ],
            'anyo',
            'usuario.login',
            // 'duracion',
            //'file_name',
            // 'created_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-eye"></i>', [
                            'canciones/view', 'id' => $model->id
                        ], [
                            'class' => 'btn btn-sm p-0 pr-1 shadow-none'
                        ]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-pen"></i>', [
                            'canciones/view', 'id' => $model->id
                        ], [
                            'class' => 'btn btn-sm p-0 shadow-none'
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-trash"></i>', null, [
                            'id' => $model->id,
                            'class' => 'btn btn-sm p-0 pl-1 shadow-none delete',
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
