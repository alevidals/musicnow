<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Videoclips */

$this->title = Yii::t('app', 'Create Videoclips');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Videoclips'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="videoclips-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>