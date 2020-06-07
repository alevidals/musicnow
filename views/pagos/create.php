<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pagos */

$this->title = Yii::t('app', 'Create Pagos');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pagos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pagos-create">

    <h3><?= Yii::t('app', 'FillThis')?></h3>

    <?= $this->render('_form', [
        'regalo' => Yii::$app->request->get('regalo'),
        'model' => $model,
        'provincias' => $provincias,
    ]) ?>

</div>
