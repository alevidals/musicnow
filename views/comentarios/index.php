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
        'template' => '{delete}',
    ],
];

if (Yii::$app->user->identity->rol == 1) {
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
