<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AlbumesCanciones */

$this->title = Yii::t('app', 'Update Albumes Canciones: {name}', [
    'name' => $model->album_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Albumes Canciones'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->album_id, 'url' => ['view', 'album_id' => $model->album_id, 'canciones_id' => $model->canciones_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="albumes-canciones-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
