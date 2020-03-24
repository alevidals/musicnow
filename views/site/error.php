<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <div class="row">
        <div class="col-12 col-lg-6 align-self-center">
            <h3>Oops! <?= Html::encode($message) ?></h3>

            <h1 class="h1-error"><?= Html::encode($exception->statusCode) ?></h1>

            <p class="font-weight-bold">
                Please contact us if you think this is a server error. Thank you.
            </p>
        </div>
        <div class="col-12 col-lg-6 align-self-center">
            <?= Html::img('@web/img/undraw_mello_otq1.png', ['class' => 'img-fluid']) ?>
        </div>
    </div>

    <?= Html::a('Volver al inicio', ['site/index'], ['class' => 'btn main-yellow font-weight-bolder mt-5']) ?>

</div>
