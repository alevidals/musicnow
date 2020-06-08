<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Generos */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Generos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->denominacion, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="generos-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
