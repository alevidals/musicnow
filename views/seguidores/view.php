<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Seguidores */

$this->title = $model->getSeguidor()->one()->login;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Seguidores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="seguidores-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'seguidor_id' => $model->seguidor_id, 'seguido_id' => $model->seguido_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'seguidor_id' => $model->seguidor_id, 'seguido_id' => $model->seguido_id], [
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
            'seguidor.login',
            'seguido.login',
        ],
        'options' => [
            'class' => 'table admin-table'
        ],
    ]) ?>

</div>
