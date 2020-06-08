<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Albumes */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Albumes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getUsuario()->one()->login, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="albumes-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
