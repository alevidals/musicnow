<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Canciones */

$this->title = Yii::t('app', 'Update Canciones: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Canciones'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');


$apiKey = Yii::$app->params['apiKey'];
$authDomain = Yii::$app->params['authDomain'];
$databaseURL = Yii::$app->params['databaseURL'];
$projectId = Yii::$app->params['projectId'];
$storageBucket = Yii::$app->params['storageBucket'];
$messagingSenderId = Yii::$app->params['messagingSenderId'];
$appId = Yii::$app->params['appId'];

$usuario_id = $model->usuario_id;

$url = Url::to(['canciones/url']);

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

    const songPrefix = 'https://firebasestorage.googleapis.com/v0/b/fir-test-64d53.appspot.com/o/temas%2F$usuario_id%2F';
    const imagePrefix = 'https://firebasestorage.googleapis.com/v0/b/fir-test-64d53.appspot.com/o/portadas%2F$usuario_id%2F';
    const suffix = '?alt=media';

    var portada = '';
    var cancion = '';
    var array = [];

    $('#bar').hide();

    $('#canciones-portada').on('change', function (e){
        portada = e.target.files[0];
        $('#cover-label').text(portada.name);
    });

    $('#canciones-cancion').on('change', function ev(e) {
        cancion = e.target.files[0];
        $('#song-label').text(cancion.name);
    });

    $('.send-btn').on('click', function ev(e) {
        e.preventDefault();

        var id = $('#canciones-id').val();

        var data = $('#w0').data("yiiActiveForm");
        $.each(data.attributes, function() {
            this.status = 3;
        });
        $('#w0').yiiActiveForm("validate");

        var storage = firebase.storage();
        var storageRef = storage.ref();

        var portadaRequest = function () {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    method: 'GET',
                    url: '$url',
                    data: {
                        id: id
                    },
                    success: function (data, code, jqXHR) {
                        var imageRef = storageRef.child('portadas/' + data.usuario_id + '/' + data.image_name);
                        imageRef.delete().then(function() {
                            var urlPortada = imagePrefix + portada.name.replace(/\s/g, '') + suffix;
                            $('#canciones-url_portada').val(urlPortada);
                            $('#canciones-image_name').val(portada.name.replace(/\s/g, ''));
                            var storageImageRef = firebase.storage().ref('portadas/$usuario_id/' + portada.name.replace(/\s/g, ''));
                            var task = storageImageRef.put(portada);

                            task.on('state_changed',

                                function progress(snapshot) {
                                    $('#bar').show();
                                    var percentage = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                                    $('#uploaderBar').text(Math.round(percentage) + '%');
                                    $('#uploaderBar').css('width', Math.round(percentage) + '%');
                                },

                                function error(err) {
                                    reject();
                                },

                                function complete() {
                                    resolve();
                                }

                            );
                        }).catch(function() {
                            reject();
                        });
                    },
                    error: function (data, code, jqXHR) {
                        reject();
                    }
                });
            });
        }

        var cancionRequest = function () {
            return new Promise(function(resolve, reject) {
                return $.ajax({
                    method: 'GET',
                    url: '$url',
                    data: {
                        id: id
                    },
                    success: function (data, code, jqXHR) {
                        var songRef = storageRef.child('temas/' + data.usuario_id + '/' + data.song_name);
                        songRef.delete()
                        .then(function() {
                            var urlCancion = songPrefix + cancion.name.replace(/\s/g, '') + suffix;
                            $('#canciones-url_cancion').val(urlCancion);
                            $('#canciones-song_name').val(cancion.name.replace(/\s/g, ''));
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
                                    reject();
                                },

                                function complete() {
                                    resolve();
                                }

                            );
                        }).catch(function() {
                            reject();
                        });
                    },
                    error: function (data, code, jqXHR) {
                        reject();
                    }
                });
            });
        }

        if (cancion) {
            array.push(cancionRequest);
        }

        if (portada) {
            array.push(portadaRequest);
        }

        Promise.all(array.map(p => p()))
        .then(() => {
            $('#w0').trigger('submit');
        })
        .catch(() => {
        });

    });

EOT;

$this->registerJsFile('@web/js/firebase-app.js');
$this->registerJsFile('@web/js/firebase-storage.js');
$this->registerJS($js);

?>
<div class="canciones-update">

<?php $form = ActiveForm::begin(); ?>

    <?= Html::activeHiddenInput($model, 'id')?>

    <?= $form->field($model, 'titulo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'album_id')->textInput() ?>

    <?= $form->field($model, 'genero_id')->textInput() ?>

    <div class="form-group required">
        <label class="col-12 px-0" for="canciones-portada">Portada</label>
        <div class="input-group mb-3">
            <div class="custom-file">
                <?= Html::fileInput('Portada', '', ['class' => 'custom-file-input', 'id' => 'canciones-portada', 'accept' => 'image/png']) ?>
                <?= Html::activeHiddenInput($model, 'url_portada', ['maxlength' => true]) ?>
                <label class="custom-file-label" id="cover-label" for="canciones-url_portada">Portada...</label>
            </div>
        </div>
        <div class="invalid-feedback image-feedback"></div>
    </div>

    <div class="form-group">
        <label class="col-12 px-0" for="canciones-cancion">Canción</label>
        <div class="input-group mb-3">
            <div class="custom-file">
                <?= Html::fileInput('Canción', '', ['class' => 'custom-file-input', 'id' => 'canciones-cancion', 'accept' => 'audio/mp3']) ?>
                <?= Html::activeHiddenInput($model, 'url_cancion', ['maxlength' => true]) ?>
                <label class="custom-file-label" id="song-label" for="canciones-url_cancion">Canción...</label>
            </div>
        </div>
        <div class="invalid-feedback song-feedback"></div>
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
