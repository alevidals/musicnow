<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Canciones */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Canciones'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getUsuario()->one()->login, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $model->titulo;

?>
<div class="canciones-update">

    <?= $this->render('_form', [
        'model' => $model,
        'generos' => $generos,
        'albumes' => $albumes,
    ]) ?>

</div>
