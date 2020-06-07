<?php

/* @var $this yii\web\View */

use yii\bootstrap4\Html;

?>
<div class="site-premium">

    <div class="row mt-5">
        <div class="col-12 col-lg-6">
            <h1><?= Yii::t('app', 'Become') ?> <span class="font-weight-bolder">PREMIUM</span></h1>
            <h5 class="mt-3"><?= Yii::t('app', 'IfPremium') ?>:</h5>
            <ul class="mt-3 premium-list">
                <li><i class="fas fa-check mt-3 mr-2 text-success"></i><?= Yii::t('app', 'DownloadSongs') ?></li>
                <li><i class="fas fa-check mt-3 mr-2 text-success"></i><?= Yii::t('app', 'PremiumName') ?></li>
                <li><i class="fas fa-check mt-3 mr-2 text-success"></i><?= Yii::t('app', 'BetterPosition') ?></li>
            </ul>
        </div>
        <div class="col-12 col-lg-6 col-md-7 offset-md-2 offset-lg-0">
            <?= Html::img('@web/img/undraw_subscriptions_1xdv.png', ['class' => 'img-fluid']) ?>
        </div>
    </div>
    <div class="mt-5 d-flex flex-column flex-md-row mx-auto align-items-center justify-content-md-around">
        <div class="card premium-card">
            <div class="card-body">
                <div class="card-title m-0">
                    <h5><?= Yii::t('app', 'ForYou') ?></h5>
                    <h6>9,99 €</h6>
                </div>
                <hr>
                <p class="card-text">
                    <?= Yii::t('app', 'EnjoyYourself') ?>
                </p>
                <div class="text-center">
                    <?php if (Yii::$app->user->identity->rol_id != 3 and Yii::$app->user->identity->rol_id != 1) : ?>
                        <?= Html::a(Yii::t('app', 'Become') . ' <span class="font-weight-bolder">PREMIUM</span>', ['pagos/create'], ['role' => 'button', 'class' => 'btn main-yellow']) ?>
                    <?php else : ?>
                        <?= Html::a(Yii::t('app', 'YouArePremium') . ' <span class="font-weight-bolder">PREMIUM</span>', null, ['role' => 'button', 'class' => 'btn main-yellow', 'disabled' => 'true']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card mt-4 mt-md-0 premium-card">
            <div class="card-body">
                <div class="card-title m-0">
                    <h5><?= Yii::t('app', 'ForAFriend') ?></h5>
                    <h6>9,99 €</h6>
                </div>
                <hr>
                <p class="card-text">
                    <?= Yii::t('app', 'GiveFriendMessage') . '!' ?>
                </p>
                <div class="text-center">
                    <?= Html::a(Yii::t('app', 'Give') . ' <span class="font-weight-bolder">PREMIUM</span>', ['pagos/create', 'regalo' => true], ['role' => 'button', 'class' => 'btn main-yellow']) ?>
                </div>
            </div>
        </div>
    </div>

</div>
