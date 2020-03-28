<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Canciones */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Canciones'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="canciones-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'titulo',
            'album.titulo',
            'genero.denominacion',
            [
                'label' => 'Canción',
                'value' => function ($model) {
                    return <<<EOT
                                <audio controls>
                                    <source src="$model->url_cancion">
                                </audio>
                        EOT;
                },
                'format' => 'raw',
            ],
            [
                'label' => 'Portada',
                'value' => function ($model) {
                    return Html::img($model->url_portada, ['class' => 'img-fluid', 'style' => 'max-width: 250px;']);
                },
                'format' => 'raw',
            ],
            'song_name',
            'image_name',
            'anyo',
            'duracion',
            'usuario_id',
            'created_at:datetime',
        ],
        'options' => [
            'class' => 'table admin-table',
        ],
    ]) ?>

</div>
