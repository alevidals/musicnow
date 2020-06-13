<?php

use app\controllers\UsuariosController;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

\yii\web\YiiAsset::register($this);
$urlGetLikes = Url::to(['canciones/get-likes']);

$confirmMessage = Yii::t('app', 'Are you sure you want to delete this item?');

$js = <<<EOT
    getFollowersData();
EOT;

$this->registerJS($js);

?>

<div class="usuarios-view">

    <span class="d-none user-id"><?= $model->id ?></span>

    <?php if ($model->url_banner) : ?>
        <?= Html::img($model->url_banner, ['class' => 'mt-4 img-fluid', 'alt' => 'banner']) ?>
    <?php endif; ?>

    <?php if ($model->id != Yii::$app->user->id) : ?>
        <?php if ($bloqueo != UsuariosController::OTHER_BLOCK
            and $bloqueo != UsuariosController::YOU_BLOCK) : ?>
            <button class="btn main-yellow mt-4 follow"></button>
            <?= Html::a(
                Yii::t('app', 'Block'),
                ['bloqueados/bloquear', 'bloqueado_id' => $model->id],
                [
                    'role' => 'button',
                    'class' => 'btn main-yellow mt-4',
                    'data' => [
                        'confirm' => Yii::t('app', 'SureLock'), 'method' => 'post',
                    ],
                ]
            ) ?>
        <?php endif; ?>
    <?php endif; ?>


    <div class="mt-3 d-sm-flex">
        <div class="text-center">
            <?= Html::img($model->url_image, ['width' => '90px', 'id' => 'image-perfil', 'class' => 'user-search-img mr-3', 'alt' => 'profile-image']) ?>
        </div>
        <div class="w-100">
            <div class="text-white text-center mt-4 d-flex justify-content-around">
                <div>
                    <span id="publicaciones" class="font-weight-bold"><?= $model->getCanciones()->count() ?></span>
                    <p><?= Yii::t('app', 'Posts') ?></p>
                </div>
                <div>
                    <button class="outline-transparent" type="button" data-toggle="modal" data-target="#seguidores-list">
                        <div>
                            <span id="seguidores" class="font-weight-bold"></span>
                            <p><?= Yii::t('app', 'Followers') ?></p>
                        </div>
                    </button>
                    <div class="modal fade" id="seguidores-list" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="row">
                                        <?php if (count($seguidores) > 0) : ?>
                                            <?php foreach ($seguidores as $usuario) : ?>
                                                <div class="col-12 fall-animation" id="follower-<?= $usuario->id ?>">
                                                    <div class="row">
                                                        <div class="col">
                                                            <?= Html::img($usuario->url_image, ['class' => 'd-inline-block user-search-img my-auto', 'width' => '30px', 'alt' => 'seguidor']) ?>
                                                        </div>
                                                        <div class="col">
                                                            <p class="d-inline-block my-auto"><?= Html::encode($usuario->login) ?></p>
                                                        </div>
                                                        <div class="col">
                                                            <button data-follower_id="<?= $usuario->id ?>" class="outline-transparent delete-follow-btn">
                                                                <i class="fas fa-trash text-danger"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <div class="col-12">
                                                <p class="my-auto"><?= Yii::t('app', 'NoFollowYou') ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <button class="outline-transparent" type="button" data-toggle="modal" data-target="#seguidos-list">
                        <div>
                            <span id="seguidos" class="font-weight-bold"></span>
                            <p><?= Yii::t('app', 'Following') ?></p>
                        </div>
                    </button>
                    <div class="modal fade" id="seguidos-list" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="row">
                                        <?php if (count($seguidos) > 0) : ?>
                                            <?php foreach ($seguidos as $usuario) : ?>
                                                <div class="col-12">
                                                    <div class="row">
                                                        <div class="col">
                                                            <?= Html::img($model->url_image, ['class' => 'd-inline-block user-search-img my-auto', 'width' => '30px', 'alt' => 'seguidos']) ?>
                                                        </div>
                                                        <div class="col">
                                                            <p class="d-inline-block my-auto"><?= Html::encode($usuario->login) ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <div class="col-12">
                                                <p class="my-auto"><?= Yii::t('app', 'YouDoNotFollow') ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (Yii::$app->user->id == $model->id || !$model->privated_account || in_array(Yii::$app->user->id, $model->getSeguidores()->select('id')->column())) : ?>

        <div class="mt-3 d-flex">
            <h1 class="d-inline-block"><?= Html::encode($model->login) ?> <?= $model->esPremium() ? '<i class="fas fa-crown"></i>' : '' ?></h1>
            <div class="dropdown d-inline-block ml-auto my-auto">
                <?php if ($model->id == Yii::$app->user->id) : ?>
                    <button class="dots-menu" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <?= Html::a(Yii::t('app', 'ConfigureProfile'), ['usuarios/configurar'], ['class' => 'dropdown-item', 'data-pjax' => 0]) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($bloqueo == UsuariosController::OTHER_BLOCK) : ?>
            <?= Html::a(
                Yii::t('app', 'Block'),
                ['bloqueados/bloquear', 'bloqueado_id' => $model->id],
                [
                    'role' => 'button',
                    'class' => 'btn main-yellow my-4',
                    'data' => [
                        'confirm' => Yii::t('app', 'SureLock'), 'method' => 'post',
                    ],
                ]
            ) ?>
            <h3><?= Yii::t('app', 'OtherBlock') ?></>
        <?php elseif ($bloqueo == UsuariosController::YOU_BLOCK) : ?>
            <h3><?= Yii::t('app', 'YouBlock') ?></h3>
            <?= Html::a(Yii::t('app', 'Desbloquear'), ['bloqueados/bloquear', 'bloqueado_id' => $model->id], ['role' => 'button', 'class' => 'btn main-yellow']) ?>
        <?php else : ?>
            <ul class="nav flex-column text-center flex-md-row d-flex justify-content-center nav-pills mb-3" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active text-uppercase" id="canciones-tab" data-toggle="tab" href="#canciones" role="tab" aria-controls="canciones" aria-selected="true"><?= Yii::t('app', 'Canciones') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="albumes-tab" data-toggle="tab" href="#albumes" role="tab" aria-controls="albumes" aria-selected="false"><?= Yii::t('app', 'Albumes') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="playlists-tab" data-toggle="tab" href="#playlists" role="tab" aria-controls="playlists1" aria-selected="false">Playlists</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="videoclips-tab" data-toggle="tab" href="#videoclips" role="tab" aria-controls="videoclips" aria-selected="false">Videoclips</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="popular-tab" data-toggle="tab" href="#popular" role="tab" aria-controls="videoclips" aria-selected="false"><?= Yii::t('app', 'Popular') ?></a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">

                <?= $this->render('_perfil-canciones', [
                    'canciones' => $canciones->all(),
                    'likes' => $likes,
                ]) ?>

                <?= $this->render('_perfil-albumes', [
                    'albumes' => $albumes,
                ]) ?>

                <?= $this->render('_perfil-playlists', [
                    'playlists' => $playlists,
                    'model' => $model,
                ]) ?>

                <?= $this->render('_perfil-videoclips', [
                    'videoclips' => $videoclips,
                    'model' => $model,
                ]) ?>

                <?= $this->render('_perfil-popular', [
                    'canciones' => $canciones,
                    'likes' => $likes,
                ]) ?>

            </div>
        <?php endif; ?>

    <?php else : ?>
        <div class="mt-3 d-flex">
            <h2 class="my-auto">
                <i class="fas fa-lock"></i>
            </h2>
            <div class="ml-3">
                <p class="font-weight-bold m-0"><?= Yii::t('app', 'PrivatedAccount') ?></p>
                <p class="m-0"><?= Yii::t('app', 'FollowToSee') ?></p>
            </div>
        </div>

    <?php endif; ?>


</div>
