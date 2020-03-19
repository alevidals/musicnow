<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Canciones */
/* @var $form yii\bootstrap4\ActiveForm */


$apiKey = getenv('apiKey');
$authDomain = getenv('authDomain');
$databaseURL = getenv('databaseURL');
$projectId = getenv('projectId');
$storageBucket = getenv('storageBucket');
$messagingSenderId = getenv('messagingSenderId');
$appId = getenv('appId');

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

    firebase . initializeApp(firebaseConfig);

    const songPrefix = 'https://firebasestorage.googleapis.com/v0/b/fir-test-64d53.appspot.com/o/temas%2F';
    const imagePrefix = 'https://firebasestorage.googleapis.com/v0/b/fir-test-64d53.appspot.com/o/portadas%2F';
    const suffix = '?alt=media';

    var portada = '';
    var cancion = '';

    $('#bar').hide();

    $('#canciones-portada').on('change', function ev(e) {
        portada = e.target.files[0];
        $('#cover-label').text(portada.name);
    });

    $('#canciones-cancion').on('change', function ev(e) {
        cancion = e.target.files[0];
        $('#song-label').text(cancion.name);
    });

    $('.send-btn').on('click', function ev(e) {
        e.preventDefault();

        // TODO: ESTO ES PARA QUE VALIDE EL FORMULARIO Y LUEGO LE HAGO SUBMIT
        var data = $('#w0').data("yiiActiveForm");
        $.each(data.attributes, function() {
            this.status = 3;
        });
        $('#w0').yiiActiveForm("validate");

        if (cancion && portada) {
            var urlPortada = imagePrefix + portada.name.replace(/\s/g, '') + suffix;
            var urlCancion = songPrefix + cancion.name.replace(/\s/g, '') + suffix;
            $('#canciones-url_portada').val(urlPortada);
            $('#canciones-url_cancion').val(urlCancion);

            var storageImageRef = firebase.storage().ref('portadas/' + portada.name.replace(/\s/g, ''));
            storageImageRef.put(portada);
            var storageSongRef = firebase.storage().ref('temas/' + cancion.name.replace(/\s/g, ''));

            var task = storageSongRef . put(cancion);

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

<div class="canciones-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'titulo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'album_id')->textInput() ?>

    <?= $form->field($model, 'genero_id')->textInput() ?>

    <label class="col-12 px-0" for="canciones-portada">Portada</label>
    <div class="input-group mb-3">
        <div class="custom-file">
            <?= Html::fileInput('Portada', '', ['class' => 'custom-file-input', 'id' => 'canciones-portada', 'accept' => 'image/png']) ?>
            <?= Html::activeHiddenInput($model, 'url_portada', ['maxlength' => true]) ?>
            <label class="custom-file-label" id="cover-label" for="canciones-url_portada">Portada...</label>
        </div>
    </div>

    <label class="col-12 px-0" for="canciones-cancion">Canción</label>
    <div class="input-group mb-3">
        <div class="custom-file">
            <?= Html::fileInput('Canción', '', ['class' => 'custom-file-input', 'id' => 'canciones-cancion', 'accept' => 'audio/mp3']) ?>
            <?= Html::activeHiddenInput($model, 'url_cancion', ['maxlength' => true]) ?>
            <label class="custom-file-label" id="song-label" for="canciones-url_cancion">Canción...</label>
        </div>
    </div>


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
