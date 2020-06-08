<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Playlists */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Playlists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getUsuario()->one()->login, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="playlists-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
