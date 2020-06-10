<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\bootstrap4\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Contact us');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-help">

        <div class="row justify-content-center">
            <div class="col-xl-8">
            <h2 class=""><?= Yii::t('app', 'HelpYou') ?></h2>
            <h6><?=Yii::t('app', 'AnyQuestions')?></h6>

                <?php $form = ActiveForm::begin([
                    'id' => 'contact-form',
                    'layout' => 'horizontal',
                ]); ?>

                    <?= $form->field($model, 'name')->textInput(['autofocus' => true])->label(Yii::t('app', 'Nombre')) ?>

                    <?= $form->field($model, 'email') ?>

                    <?= $form->field($model, 'subject')->label(Yii::t('app', 'Subject')) ?>

                    <?= $form->field($model, 'body')->textarea(['rows' => 6])->label(Yii::t('app', 'Body')) ?>

                    <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                        'imageOptions' => ['class' => 'col-sm-3', 'style' => 'padding: 0'],
                        'options' => ['class' => 'form-control col-sm-7', 'style' => 'display: inline'],
                    ])->label(Yii::t('app', 'Verification Code')) ?>

                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-warning', 'name' => 'contact-button']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
</div>