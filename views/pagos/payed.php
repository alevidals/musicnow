<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pagos */

?>
<div class="pagos-payed">

    <h1 class="mt-5"><?= Yii::t('app', 'Congratulations') ?></h1>

    <p><?= Yii::t('app', 'CongratulationsMessage') ?></p>

    <?= Html::a(Yii::t('app', 'DownloadInvoice'), ['pagos/get-invoice'], ['role' => 'button', 'class' => 'btn main-yellow', 'data-pjax' => 0]) ?>

    <div class="row mt-5">
        <?= Html::img('@web/img/undraw_super_thank_you_obwk.png', ['class' => 'img-fluid col-12 col-md-6 mx-auto']) ?>
    </div>


</div>
