<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Likes */

$this->title = $model->usuario_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Likes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="likes-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'usuario_id' => $model->usuario_id, 'cancion_id' => $model->cancion_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'usuario_id' => $model->usuario_id, 'cancion_id' => $model->cancion_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'usuario_id',
            'cancion_id',
        ],
    ]) ?>

</div>
