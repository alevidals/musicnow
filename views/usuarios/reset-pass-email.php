<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

\yii\web\YiiAsset::register($this);
?>
<div class="usuarios-reset-pass-email">

    <h2 class="my-5"><?= Yii::t('app', 'enter-email') ?></h2>

    <?php $form = ActiveForm::begin(); ?>
        <div class="row p-0">
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn main-yellow']) ?>
                </div>
            </div>
        </div>


    <?php ActiveForm::end(); ?>

</div>
