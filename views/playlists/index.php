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
    ['class' => 'yii\grid\ActionColumn'],
];

if (Yii::$app->user->identity->rol == 1) {
    $columns = array_merge([[
        'attribute' => 'usuario.login'
    ]], $columns);
}

?>
<div class="playlists-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Playlists'), ['create'], ['class' => 'btn main-yellow']) ?>
    </p>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

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
