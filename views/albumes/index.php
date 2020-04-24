<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AlbumesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Albumes');
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    'titulo',
    'anyo',
    'created_at:datetime',
    [
        'class' => 'yii\grid\ActionColumn',
        'header' => Yii::t('app', 'Actions'),
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
        <?= Html::a(Yii::t('app', 'Create Albumes'), ['create'], ['class' => 'btn main-yellow']) ?>
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
