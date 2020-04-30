<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = Yii::t('app', 'Notifications');
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);
?>
    <h1 class="mt-3"><?= Yii::t('app', 'Notifications') ?></h1>

    <?php if (count($notificaciones) > 0) : ?>
        <div class="row">
            <?php foreach ($notificaciones as $notificacion) : ?>
                <div class="col-12 col-lg-6 col-xl-4 mb-3 fall-animation" id="notificacion-<?= $notificacion['id'] ?>">
                    <?= Html::img($notificacion['url_image'], ['class' => 'user-search-img', 'width' => '40px', 'alt' => 'logo']) ?>
                    <span class="ml-3"><?= $notificacion['login'] . ' ' . Yii::t('app', 'WantToFollow')?></span>
                    <div class="float-right">
                        <button data-id="<?= $notificacion['id'] ?>" class="outline-transparent action-btn my-auto accept request-btn"><i class="fas fa-check-circle green-check"></i></button>
                        <button data-id="<?= $notificacion['id'] ?>" class="outline-transparent action-btn delete request-btn"><i class="fas fa-times-circle red-hearth"></i></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <h4 class="text-center"><?= Yii::t('app', 'NoNotification') ?></h4>
    <?php endif; ?>

</div>
