<?php

/* @var $this yii\web\View */

use yii\bootstrap4\Html;

?>
<div class="site-premium">

    <div class="row mt-5">
        <div class="col-12 col-lg-6">
            <h1 class="text-center"><?= Yii::t('app', 'Become') ?> <span class="font-weight-bolder">PREMIUM</span></h1>
            <h5 class="text-center mt-3"><?= Yii::t('app', 'IfPremium') ?>:</h5>
            <ul class="ml-5 mt-3 premium-list">
                <li><i class="fas fa-check mt-3 mr-2 text-success"></i><?= Yii::t('app', 'DownloadSongs') ?></li>
                <li><i class="fas fa-check mt-3 mr-2 text-success"></i><?= Yii::t('app', 'PremiumName') ?></li>
                <li><i class="fas fa-check mt-3 mr-2 text-success"></i><?= Yii::t('app', 'BetterPosition') ?></li>
            </ul>
        </div>
        <div class="col-12 col-lg-6 col-md-7 offset-md-2 offset-lg-0">
            <?= Html::img('@web/img/undraw_subscriptions_1xdv.png', ['class' => 'img-fluid']) ?>
        </div>
    </div>
    <div class="text-center mt-5">
        <?= Html::a(Yii::t('app', 'Become') . ' <span class="font-weight-bolder">PREMIUM</span>', ['pagos/create'], ['role' => 'button', 'class' => 'btn main-yellow']) ?>
    </div>

</div>
