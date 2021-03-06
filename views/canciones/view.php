<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Canciones */

$this->title = $model->titulo;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Canciones'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="canciones-view">

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
                'label' => Yii::t('app', 'Song'),
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
                'label' => Yii::t('app', 'Cover'),
                'value' => function ($model) {
                    return Html::img($model->url_portada, ['class' => 'img-fluid', 'style' => 'max-width: 250px;', 'alt' => 'portada']);
                },
                'format' => 'raw',
            ],
            'song_name',
            'image_name',
            'anyo',
            'duracion',
            'usuario.login',
            'created_at:datetime',
        ],
        'options' => [
            'class' => 'table admin-table',
        ],
    ]) ?>

</div>
