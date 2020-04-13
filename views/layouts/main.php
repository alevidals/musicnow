<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\services\Utility;
use app\widgets\Alert;
use kartik\dialog\Dialog;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);
$this->registerJS(Utility::GET_COOKIE);

$urlCookie = Url::to(['site/cookie']);
$urlGetNoReadMessages = Url::to(['usuarios/get-no-read-messages']);

$cookieMessage = Yii::t('app', 'CookieMessage');

$js = <<<EOT

    var mensajes = 0;

    if (getCookie('cookie-accept') == null) {
        $( document ).ready(function() {
            krajeeDialogCust2.confirm("$cookieMessage", function (result) {
                if (result) {
                    window.location="$urlCookie";
                } else {
                    window.location="http://google.es";
                }
            });
        });
    }

    setInterval(function(){
        getNoReadMessagesMain();
    }, 5000);

    function getNoReadMessagesMain() {
        $.ajax({
            method: 'GET',
            url: '$urlGetNoReadMessages&total=' + mensajes,
            success: function (data) {
                if (data.count > 0) {
                    $('.messages-number').html(data.count);
                } else {
                    $('.messages-number').html('');
                }
                if (data.count > mensajes) {
                    $('.chat-notification').trigger('play');
                    $('.alert-box').html('');
                    data.mensajes.forEach(element => {
                        var time = element.created_at.split(' ')[1];
                        $('.alert-box').append(`
                            <a href="/index.php?r=chat/chat" class="text-decoration-none">
                                <div class="toast" data-delay="5000">
                                    <div class="toast-header">
                                        <img src="\${element.url_image}" class="rounded mr-2 navbar-logo" alt="profile-img">
                                        <strong class="mr-auto">\${element.login}</strong>
                                        <small class="ml-3">\${time}</small>
                                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="toast-body">
                                        \${element.mensaje}
                                    </div>
                                </div>
                            </a>
                        `);
                    });
                    $('.toast').toast('show');
                }
                mensajes = data.count;
            }
        });
    }

    GreenAudioPlayer.init({
        selector: '.player',
        stopOthersOnPlay: true
    });
    $('.full-player').css('display', 'none');
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
<?php $this->beginBody() ?>

    <div aria-live="polite" aria-atomic="true" style="position: relative;">
        <div class="alert-box" style="position: absolute; top: 50px; right: 70px; z-index: 1050;">
        </div>
    </div>

<div class="wrap">
    <?php
    NavBar::begin([
        // 'brandLabel' => Yii::$app->name,
        'brandLabel' => Html::img('@web/img/logo.png', ['class' => 'navbar-logo', 'alt' => 'logo']),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-dark bg-dark navbar-expand-md fixed-top',
        ],
        'collapseOptions' => [
            'class' => 'justify-content-end',
        ],
    ]);
    if (!Yii::$app->user->isGuest) {
        if (Yii::$app->user->identity->rol == 1) {
            $items = [
                ['label' => Yii::t('app', 'Home'), 'url' => ['/site/admin-index']],
                ['label' => Yii::t('app', 'Generos'), 'url' => ['/generos/index']],
                ['label' => Yii::t('app', 'Albumes'), 'url' => ['/albumes/index']],
                ['label' => Yii::t('app', 'Canciones'), 'url' => ['/canciones/index']],
                ['label' => Yii::t('app', 'Usuarios'), 'url' => ['/usuarios/index']],
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
                    ['label' => Yii::t('app', 'Albumes'), 'url' => ['/albumes/index'], 'options' => ['class' => 'my-auto']],
                    ['label' => Yii::t('app', 'Canciones'), 'url' => ['/canciones/index'], 'options' => ['class' => 'my-auto']],
                    ['label' => 'Chat<span class="badge badge-warning ml-1 messages-number"></span>', 'url' => ['/chat/chat'], 'options' => ['class' => 'my-auto']],
                    [
                        'label'=> Yii::t('app', 'Language'),
                        'options' => ['class' => 'my-auto'],
                        'items' => [
                            ['label' => 'Español', 'url' => ['/site/idioma', 'lang' => 'es-ES']],
                            ['label' => 'English', 'url' => ['/site/idioma', 'lang' => 'en']],
                        ]
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
                'label'=> Yii::t('app', 'Language'),
                'options' => ['class' => 'my-auto'],
                'items' => [
                    ['label' => 'Español', 'url' => ['/site/idioma', 'lang' => 'es-ES']],
                    ['label' => 'English', 'url' => ['/site/idioma', 'lang' => 'en']],
                ]
            ],
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'encodeLabels'=> false,
        'items' => $items
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

<audio class="chat-notification">
    <source src="/sounds/notification.mp3">
</audio>

<div class="full-player row ml-0">
    <div class="info-song col-12 col-lg-4 col-xl-2 ml-0 row">
        <img alt="song-cover col-2" height="60px">
        <div class="artist-info my-auto col text-center">
            <p class="m-0"></p>
            <small class="text-white"></small>
        </div>
    </div>
    <div class="player col-12 col-lg-8 col-xl-10">
        <audio autoplay>
            <source>
        </audio>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="float-left">&copy; My Company <?= date('Y') ?></p>

        <p class="float-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
