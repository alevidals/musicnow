<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Bloqueados */

$this->title = $model->bloqueador_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bloqueados'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="bloqueados-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'bloqueador_id' => $model->bloqueador_id, 'bloqueado_id' => $model->bloqueado_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'bloqueador_id' => $model->bloqueador_id, 'bloqueado_id' => $model->bloqueado_id], [
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
            'bloqueador_id',
            'bloqueado_id',
        ],
        'options' => [
            'class' => 'table admin-table'
        ],
    ]) ?>

</div>
