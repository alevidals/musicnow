<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Videoclips */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Videoclips'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getUsuario()->one()->login, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="videoclips-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
