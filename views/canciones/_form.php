<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Canciones */
/* @var $form yii\bootstrap4\ActiveForm */


$apiKey = Yii::$app->params['apiKey'];
$authDomain = Yii::$app->params['authDomain'];
$databaseURL = Yii::$app->params['databaseURL'];
$projectId = Yii::$app->params['projectId'];
$storageBucket = Yii::$app->params['storageBucket'];
$messagingSenderId = Yii::$app->params['messagingSenderId'];
$appId = Yii::$app->params['appId'];
$firebaseUrl = Yii::$app->params['firebaseUrl'];

$usuario_id = $model->usuario_id;

$js = <<<EOT


    const songPrefix = '$firebaseUrl/temas%2F$usuario_id%2F';
    const imagePrefix = '$firebaseUrl/portadas%2F$usuario_id%2F';
    const suffix = '?alt=media';

    var portada = '';
    var cancion = '';
    var portadaFlag = false;
    var cancionFlag = false;
    var valid = false;
    $('#bar').hide();

    $.fn.filepond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginImageValidateSize,
        FilePondPluginFileValidateSize,
        FilePondPluginFileValidateType,
    );

    $('.filepond-image').filepond({
        labelIdle: 'Introduza su portada',
        imageValidateSizeMaxWidth: 540,
        imageValidateSizeMaxHeight: 540,
        maxFileSize: '5MB',
        acceptedFileTypes: ['image/png'],
    });

    $('.filepond-song').filepond({
        labelIdle: 'Introduza su canción',
        acceptedFileTypes: ['audio/mp3'],
        maxFileSize: '20MB',
    });

    $('.filepond-image').on('FilePond:addfile', function(e) {
        portadaFlag = true;
    });

    $('.filepond-song').on('FilePond:addfile', function(e) {
        cancionFlag = true;
    });

    $('.filepond-image').on('FilePond:removefile', function(e) {
        portadaFlag = false;
        portada = '';
    });

    $('.filepond-song').on('FilePond:removefile', function(e) {
        cancionFlag = false;
        cancion = '';
    });

    $('.filepond-image').on('FilePond:error', function(e) {
        $('.image-feedback').text('Debes introducir una imágen que cumpla los requisitos');
        $('.image-feedback').css('display', 'block');
        portadaFlag = false;
        portada = '';
    });

    $('.filepond-song').on('FilePond:error', function(e) {
        $('.song-feedback').text('Debes introducir una canción que cumpla los requisitos');
        $('.song-feedback').css('display', 'block');
        console.log('error-song');
        cancionFlag = false;
        cancion = '';
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

    $('#canciones-portada').on('change', function ev(e) {
        portada = e.target.files[0];
        $('#cover-label').text(portada.name);
        $('.image-feedback').text('');
        $('.image-feedback').css('display', 'none');
    });

    $('#canciones-cancion').on('change', function ev(e) {
        cancion = e.target.files[0];
        $('#song-label').text(cancion.name);
        $('.song-feedback').text('');
        $('.song-feedback').css('display', 'none');
    });

    $('.send-btn').on('click', function ev(e) {
        e.preventDefault();

        var data = $('#w0').data("yiiActiveForm");
        $.each(data.attributes, function() {
            this.status = 3;
        });
        $('#w0').yiiActiveForm("validate");

        if (cancion && portada) {

            $('.invalid-feedback').each(function() {
                if ($(this).is(':not(:empty)')) {
                    valid = false;
                    return false;
                } else {
                    valid = true;
                }
            });

            if (valid) {
                var urlPortada = imagePrefix + portada.name.replace(/\s/g, '') + suffix;
                var urlCancion = songPrefix + cancion.name.replace(/\s/g, '') + suffix;
                $('#canciones-url_portada').val(urlPortada);
                $('#canciones-url_cancion').val(urlCancion);
                $('#canciones-song_name').val(cancion.name.replace(/\s/g, ''));
                $('#canciones-image_name').val(portada.name.replace(/\s/g, ''));

                var storageImageRef = firebase.storage().ref('portadas/$usuario_id/' + portada.name.replace(/\s/g, ''));
                storageImageRef.put(portada);
                var storageSongRef = firebase.storage().ref('temas/$usuario_id/' + cancion.name.replace(/\s/g, ''));

                var task = storageSongRef.put(cancion);

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
        } else {
            if (!portadaFlag) {
                $('.image-feedback').text('Debes introducir una imágen');
                $('.image-feedback').css('display', 'block');
            }
            if (!cancionFlag) {
                $('.song-feedback').text('Debes introducir una canción');
                $('.song-feedback').css('display', 'block');
            }
        }

    function isEmpty( el ){
        return !$.trim(el.html())
    }

    });
EOT;

$this->registerJsFile('@web/js/firebase-app.js');
$this->registerJsFile('@web/js/firebase-storage.js');
$this->registerJs($js);

?>

<div class="canciones-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'titulo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'album_id')->dropDownList($albumes) ?>

    <?= $form->field($model, 'genero_id')->dropDownList($generos) ?>

    <div class="form-group">
        <label class="col-12 px-0" for="canciones-portada">Portada</label>
        <?= Html::fileInput('Portada', '', ['class' => 'filepond-image col-12 col-md-6', 'id' => 'canciones-portada', 'accept' => 'image/png']) ?>
        <div class="invalid-feedback image-feedback"></div>
        <?= Html::activeHiddenInput($model, 'url_portada', ['maxlength' => true]) ?>
    </div>

    <div class="form-group">
        <label class="col-12 px-0" for="canciones-cancion">Canción</label>
        <?= Html::fileInput('Canción', '', ['class' => 'filepond-song col-12 col-md-6', 'id' => 'canciones-cancion', 'accept' => 'audio/mp3']) ?>
        <div class="invalid-feedback song-feedback"></div>
        <?= Html::activeHiddenInput($model, 'url_cancion', ['maxlength' => true]) ?>
    </div>

    <?= Html::activeHiddenInput($model, 'song_name', ['maxlength' => true]) ?>
    <?= Html::activeHiddenInput($model, 'image_name', ['maxlength' => true]) ?>

    <?= $form->field($model, 'anyo')->textInput() ?>

    <?= $form->field($model, 'duracion')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success send-btn']) ?>
    </div>

    <div class="progress mt-5" id="bar">
        <div class="progress-bar text-dark main-yellow font-weight-bold" id="uploaderBar" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
