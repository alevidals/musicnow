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

    $('.send-btn').prop('disabled', true);

    $.fn.filepond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginImageValidateSize,
        FilePondPluginFileValidateSize,
        FilePondPluginFileValidateType,
    );

    $('.filepond').filepond({
        labelIdle: 'Introduza su imágen',
        imageValidateSizeMaxWidth: 200,
        imageValidateSizeMaxHeight: 200,
        maxFileSize: '5MB',
        acceptedFileTypes: ['image/png'],
    });

    $('.filepond').on('FilePond:addfile', function(e) {
        $('.send-btn').prop('disabled', false);
        $('.send-btn').removeClass('btn-secondary');
        $('.send-btn').addClass('main-yellow');
    });

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

    $('.delete-btn').on('click', function ev(e) {
        e.preventDefault();

        if(confirm('¿Eliminar?')) {
            $('.delete-hidden-input').val('delete');
            var storage = firebase.storage();
            var storageRef = storage.ref();
            var imageRef = storageRef.child('image/perfil/$usuario_id/perfil.png');
            imageRef.delete().then(function() {
                $('#w0').trigger('submit');
            });
        }

    });

EOT;

$this->registerJsFile('@web/js/firebase-app.js');
$this->registerJsFile('@web/js/firebase-storage.js');
$this->registerJs($js);

?>

<div class="usuarios-image">

    <?= Html::img($model->url_image, ['width' => '200px', 'id' => 'image-perfil', 'class' => 'my-3 mx-auto']) ?>

    <?php $form = ActiveForm::begin(); ?>

    <?= Html::fileInput('imagen', '', ['class' => 'filepond col-12 col-md-6', 'id' => 'usuarios-image', 'accept' => 'image/png']) ?>

    <input type="hidden" name="delete" class="delete-hidden-input">

    <?= Html::activeHiddenInput($model, 'url_image', ['maxlength' => true]) ?>

    <?= Html::activeHiddenInput($model, 'image_name', ['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-secondary send-btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php $form = ActiveForm::begin(); ?>
    <?= Html::button(Yii::t('app', 'Delete'), ['class' => 'btn btn-danger delete-btn']) ?>
    <?php ActiveForm::end(); ?>

    <div class="progress mt-5" id="bar">
        <div class="progress-bar text-dark main-yellow font-weight-bold" id="uploaderBar" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

</div>
