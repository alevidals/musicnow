<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

\yii\web\YiiAsset::register($this);


$apiKey = Yii::$app->params['apiKey'];
$authDomain = Yii::$app->params['authDomain'];
$databaseURL = Yii::$app->params['databaseURL'];
$projectId = Yii::$app->params['projectId'];
$storageBucket = Yii::$app->params['storageBucket'];
$messagingSenderId = Yii::$app->params['messagingSenderId'];
$appId = Yii::$app->params['appId'];
$firebaseUrl = Yii::$app->params['firebaseUrl'];

$usuario_id = Yii::$app->user->id;
$image = $model->url_image;

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

    var storageRef = firebase.storage().ref();

    var listRef = storageRef.child('image/perfil/$usuario_id');

    listRef.listAll().then(function(res) {
        if (res.items.length == 0) {
            $('#image-perfil').attr('src', 'https://firebasestorage.googleapis.com/v0/b/music-test-77a40.appspot.com/o/image%2Fperfil%2Fall%2Fblank-profile.png?alt=media&token=687f6533-77f8-4ab0-b87d-65c855488ce0');
        } else {
            $('#image-perfil').attr('src', '$image');
        }
    });

EOT;

$this->registerJsFile('@web/js/firebase-app.js');
$this->registerJsFile('@web/js/firebase-storage.js');
$this->registerJs($js);

?>

<div class="usuarios-view">

    <?= Html::img('@web/img/banner.png', ['class' => 'img-fluid']) ?>

    <?= Html::img('', ['width' => '100px', 'id' => 'image-perfil', 'class' => 'mt-3']) ?>

    <div class="mt-3 d-flex">
        <h1 class="d-inline-block"><?= $model->login?></h1>
        <div class="dropdown d-inline-block ml-auto my-auto">
            <button class="dots-menu" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-h"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <?= Html::a('Editar perfil', ['usuarios/update', 'id' => Yii::$app->user->id], ['class' => 'dropdown-item']) ?>
                <?= Html::a('Editar imágen de perfil', ['usuarios/imagen', 'id' => Yii::$app->user->id], ['class' => 'dropdown-item']) ?>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        <li class="nav-item ml-auto">
            <a class="nav-link active text-uppercase" id="canciones-tab" data-toggle="tab" href="#canciones" role="tab" aria-controls="canciones" aria-selected="true">Canciones</a>
        </li>
        <li class="nav-item mr-auto">
            <a class="nav-link text-uppercase" id="albumes-tab" data-toggle="tab" href="#albumes" role="tab" aria-controls="albumes" aria-selected="false">Albumes</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="canciones" role="tabpanel" aria-labelledby="canciones-tab">
            <div class="row">
                <?php if (count($canciones) > 0) : ?>
                    <?php foreach ($canciones as $cancion) : ?>
                        <div class="col-12 col-md-4 col-lg-3">
                            <button style="background-color: transparent; border: none; outline: none;" data-toggle="modal" data-target="#song-<?= $cancion->id ?>">
                                <?= Html::img($cancion->url_portada, ['class' => 'img-fluid'])?>
                            </button>
                            <div class="modal fade" id="song-<?= $cancion->id ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <?= Html::img($cancion->url_portada, ['class' => 'img-fluid']) ?>
                                                </div>
                                                <div class="col-lg-4">
                                                    <p>Comentarios...</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else :?>
                    <div class="row mt-5 justify-content-center text-center">
                        <div class="col-12">
                            <h2>Parece que no ha subido ninguna canción aún.</h2>
                        </div>
                        <div class="col-10 col-lg-6">
                            <?= Html::img('@web/img/undraw_recording_lywr.png', ['class' => 'img-fluid']) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="tab-pane fade" id="albumes" role="tabpanel" aria-labelledby="albumes-tab">
            <div class="row">
                <?php if (count($albumes) > 0) : ?>
                    <?php foreach ($albumes as $album) : ?>
                        <div class="col-lg-3">
                            <h2><?= $album->titulo?></h2>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="row mt-5 justify-content-center text-center">
                        <div class="col-12">
                            <h2>Parece que no tiene ningún álbum aún.</h2>
                        </div>
                        <div class="col-10 col-lg-4">
                            <?= Html::img('@web/img/undraw_no_data_qbuo.png', ['class' => 'img-fluid']) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>


</div>
