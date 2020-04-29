<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SolicitudesSeguimiento */

$this->title = Yii::t('app', 'Create Solicitudes Seguimiento');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Solicitudes Seguimientos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="solicitudes-seguimiento-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
