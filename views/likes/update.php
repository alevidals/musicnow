<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Likes */

$this->title = Yii::t('app', 'Update Likes: {name}', [
    'name' => $model->usuario_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Likes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->usuario_id, 'url' => ['view', 'usuario_id' => $model->usuario_id, 'cancion_id' => $model->cancion_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="likes-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
