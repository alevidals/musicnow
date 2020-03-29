<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Seguidores */

$this->title = Yii::t('app', 'Create Seguidores');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Seguidores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seguidores-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
