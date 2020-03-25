<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */
/* @var $form yii\bootstrap4\ActiveForm */


$apiKey = Yii::$app->params['apiKey'];
$authDomain = Yii::$app->params['authDomain'];
$databaseURL = Yii::$app->params['databaseURL'];
$projectId = Yii::$app->params['projectId'];
$storageBucket = Yii::$app->params['storageBucket'];
$messagingSenderId = Yii::$app->params['messagingSenderId'];
$appId = Yii::$app->params['appId'];
$firebaseUrl = Yii::$app->params['firebaseUrl'];

$usuario_id = Yii::$app->user->id;

$js = <<<EOT

    var firebaseConfig = {
        apiKey: "$apiKey",
        authDomain: "$authDomain",
        databaseURL: "$databaseURL",
        projectId: "$projectId",
        storageBucket: "$storageBucket",
        messagingSenderId: "$messagingSenderId",
        appId: "$appId",
    };

    firebase.initializeApp(firebaseConfig);

    const imagePrefix = '$firebaseUrl/image%2Fperfil%2F$usuario_id%2F';
    const suffix = '?alt=media';

    var image = '';

    $('#bar').hide();

    $('#usuarios-image').on('change', function ev(e) {
        image = e.target.files[0];
        $('#image-label').text(image.name);
    });

    $('.send-btn').on('click', function ev(e) {
        e.preventDefault();

        if (image) {
            var urlImage = imagePrefix + 'perfil.png' + suffix;
            $('#usuarios-url_image').val(urlImage);
            $('#usuarios-image_name').val('perfil.png');

            var storageImageRef = firebase.storage().ref('image/perfil/$usuario_id/perfil.png');
            var task = storageImageRef.put(image);

            task.on('state_changed',

                function progress(snapshot) {
                    $('#bar').show();
                    var percentage = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                    $('#uploaderBar').text(Math.round(percentage) + '%');
                    $('#uploaderBar').css('width', Math.round(percentage) + '%');
                },

                function error(err) {
                },

                function complete() {
                    $('#w0').trigger('submit');
                }

            );
        }

    });

EOT;

$this->registerJsFile('@web/js/firebase-app.js');
$this->registerJsFile('@web/js/firebase-storage.js');
$this->registerJs($js);

?>

<div class="usuarios-image">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group">
        <label class="col-12 px-0" for="usuarios-image">Imágen de perfil</label>
        <div class="input-group mb-3">
            <div class="custom-file">
                <?= Html::fileInput('Imágen', '', ['class' => 'custom-file-input', 'id' => 'usuarios-image', 'accept' => 'image/png']) ?>
                <?= Html::activeHiddenInput($model, 'url_image', ['maxlength' => true]) ?>
                <label class="custom-file-label" id="image-label" for="usuarios-url_image">Imágen...</label>
            </div>
        </div>
    </div>

    <?= Html::activeHiddenInput($model, 'image_name', ['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success send-btn']) ?>
    </div>

    <div class="progress mt-5" id="bar">
        <div class="progress-bar text-dark main-yellow font-weight-bold" id="uploaderBar" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>