<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AlbumesCanciones */

$this->title = Yii::t('app', 'Create Albumes Canciones');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Albumes Canciones'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="albumes-canciones-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
