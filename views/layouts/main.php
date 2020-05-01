<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\models\Usuarios;
use app\widgets\Alert;
use kartik\dialog\Dialog;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

AppAsset::register($this);

$isLogued = !Yii::$app->user->isGuest;

$js = <<<EOT
    if ('$isLogued') {
        getFollowersNumber();
        getNewNotifications();
        setInterval(function () {
            getNewNotifications();
            getStatusFromUsers();
        }, 5000);
    }

    GreenAudioPlayer.init({
        selector: '.player',
        stopOthersOnPlay: true
    });
    $('.audio-player').css('display', 'none');
    $('.player').css('display', 'none');
EOT;

Dialog::widget([
    'libName' => 'krajeeDialogCust2',
    'options' => [
        'draggable' => false,
        'closable' => false,
        'size' => Dialog::SIZE_MEDIUM,
        'type' => Dialog::TYPE_WARNING,
        'title' => 'MUS!C NOW',
        'btnOKClass' => 'btn-warning',
        'btnOKLabel' => Yii::t('app', 'Accept'),
        'btnCancelClass' => 'btn-secondary',
        'btnCancelLabel' => Yii::t('app', 'Reject'),
    ],
]);

$this->registerJS($js);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" integrity="sha256-+N4/V/SbAFiW1MPBCXnfnP9QSN3+Keu+NlB+0ev/YKQ=" crossorigin="anonymous" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" defer></script>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php Pjax::begin(); ?>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        // 'brandLabel' => Yii::$app->name,
        'brandLabel' => Html::img('@web/img/logo.png', ['class' => 'navbar-logo', 'alt' => 'logo']),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-dark navbar-expand-md fixed-top',
        ],
        'collapseOptions' => [
            'class' => 'justify-content-end',
        ],
    ]);
    if (!Yii::$app->user->isGuest) {
        if (Yii::$app->user->identity->rol_id == 1) {
            $items = [
                [
                    'label' => '<i class="fas fa-sun"></i>
                                <div class="custom-control custom-switch d-inline-block">
                                    <input type="checkbox" class="custom-control-input " id="darkSwitch">
                                    <label class="custom-control-label" for="darkSwitch"><i class="fas fa-moon"></i></label>
                                </div>',
                ],
                ['label' => Yii::t('app', 'Home'), 'url' => ['/site/admin-index']],
                ['label' => Yii::t('app', 'Generos'), 'url' => ['/generos/index']],
                ['label' => Yii::t('app', 'Albumes'), 'url' => ['/albumes/index']],
                ['label' => Yii::t('app', 'Canciones'), 'url' => ['/canciones/index']],
                ['label' => Yii::t('app', 'Usuarios'), 'url' => ['/usuarios/index']],
                [
                    'label' => Yii::t('app', 'Language'),
                    'options' => ['class' => 'my-auto'],
                    'items' => [
                        ['label' => 'Español', 'url' => ['/site/idioma', 'lang' => 'es-ES']],
                        ['label' => 'English', 'url' => ['/site/idioma', 'lang' => 'en']],
                    ],
                ],
                Yii::$app->user->isGuest ? (
                    [
                        'label' => 'Entra',
                        'items' => [
                            ['label' => 'Login', 'url' => ['/usuarios/login']],
                        ],
                    ]
                ) : (
                    [
                        'label' => Yii::$app->user->identity->nombre,
                        'items' => [
                            [
                                'label' => 'Logout',
                                'url' => [
                                    '/usuarios/logout',
                                    'method' => 'post',
                                ],
                                'linkOptions' => [
                                    'data-method' => 'post',
                                ],
                            ],
                        ],
                    ]
                ),
            ];
        } else {
            $items = [
                    ['label' => Yii::t('app', 'Home'), 'url' => ['/site/index'], 'options' => ['class' => 'my-auto']],
                    ['label' => 'Chat<span class="badge badge-warning ml-1 messages-number"></span>', 'url' => ['/chat/chat'], 'options' => ['class' => 'my-auto']],
                    ['label' => 'Notificaciones<span class="badge badge-warning ml-1 notifications-number">', 'url' => ['/usuarios/notificaciones'], 'options' => ['class' => 'my-auto']],
                    ['label' => Yii::t('app', 'Playlists'), 'url' => ['/playlists/index'], 'options' => ['class' => 'my-auto']],
                    [
                        'label' => Yii::t('app', 'Manage'),
                        'options' => ['class' => 'my-auto'],
                        'items' => [
                            ['label' => Yii::t('app', 'Albumes'), 'url' => ['/albumes/index']],
                            ['label' => Yii::t('app', 'Canciones'), 'url' => ['/canciones/index']],
                            ['label' => Yii::t('app', 'Comments'), 'url' => ['/comentarios/index']],
                        ],
                    ],
                    Yii::$app->user->isGuest ? (
                        [
                            'label' => Yii::t('app', 'Entrar'),
                            'items' => [
                                ['label' => 'Login', 'url' => ['/usuarios/login']],
                            ],
                        ]
                    ) : (
                        [
                            'label' => Html::img(Yii::$app->user->identity->url_image, ['class' => 'user-search-img', 'width' => '40px', 'alt' => 'logo']),
                            'items' => [
                                ['label' => Yii::t('app', 'My account'), 'url' => ['usuarios/perfil', 'id' => Yii::$app->user->id]],
                                ['label' => Yii::t('app', 'ConfigureProfile'), 'url' => ['usuarios/configurar', 'id' => Yii::$app->user->id]],
                                [
                                    'label' => Yii::t('app', 'Logout'),
                                    'url' => [
                                        '/usuarios/logout',
                                        'method' => 'post',
                                    ],
                                    'linkOptions' => [
                                        'data-method' => 'post',
                                    ],
                                ],
                            ],
                        ]
                    ),
                ];
        }
    } else {
        $items = [
            [
                'label' => Yii::t('app', 'Language'),
                'options' => ['class' => 'my-auto'],
                'items' => [
                    ['label' => 'Español', 'url' => ['/site/idioma', 'lang' => 'es-ES']],
                    ['label' => 'English', 'url' => ['/site/idioma', 'lang' => 'en']],
                ],
            ],
            [
                'label' => '<i class="fas fa-sun"></i>
                            <div class="custom-control custom-switch d-inline-block">
                                <input type="checkbox" class="custom-control-input " id="darkSwitch">
                                <label class="custom-control-label" for="darkSwitch"><i class="fas fa-moon"></i></label>
                            </div>',
            ],
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'encodeLabels' => false,
        'items' => $items,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'options' => [
                'class' => 'mt-3',
            ],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<?php Pjax::end(); ?>

<audio class="chat-notification">
    <source src="/sounds/notification.mp3">
</audio>


<div aria-live="polite" aria-atomic="true" style="position: relative;">
    <div class="alert-box" style="position: fixed; top: 50px; right: 70px; z-index: 1050;">
    </div>
</div>

<div class="audio-player fixed-player">
    <div>
        <button class="outline-transparent hide-player">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>
    <div class="full-player w-100 row ml-0">
        <div class="info-song col-12 col-lg-4 col-xl-3 ml-0 row">
            <img class="height-60" src=":" alt="song-cover col-2">
                <button class="my-auto ml-2 action-btn backward-btn outline-transparent"><i class="fas fa-backward"></i></button>
                <div class="artist-info my-auto col text-center">
                    <p class="m-0"></p>
                    <small></small>
                </div>
                <button class="my-auto mr-2 action-btn forward-btn outline-transparent"><i class="fas fa-forward"></i></button>
        </div>
        <div class="player col-12 col-lg-8 col-xl-9">
            <audio id="audio">
                <source src=":">
            </audio>
        </div>
    </div>
</div>


<footer class="footer">
    <div class="container">
        <p class="float-left">&copy; My Company <?= date('Y') ?></p>
        <i class="fas fa-sun"></i>
        <div class="custom-control custom-switch d-inline-block">
            <input type="checkbox" class="custom-control-input " id="darkSwitch">
            <label class="custom-control-label" for="darkSwitch"><i class="fas fa-moon"></i></label>
        </div>
        <p class="float-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
