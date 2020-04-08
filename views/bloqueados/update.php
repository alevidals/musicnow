<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Bloqueados */

$this->title = Yii::t('app', 'Update Bloqueados: {name}', [
    'name' => $model->bloqueador_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bloqueados'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->bloqueador_id, 'url' => ['view', 'bloqueador_id' => $model->bloqueador_id, 'bloqueado_id' => $model->bloqueado_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="bloqueados-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
