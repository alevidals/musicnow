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

$js = <<<EOT
    if (getCookie('cookie-accept') == null) {
        $( document ).ready(function() {
            krajeeDialogCust2.confirm("Utilizamos cookies para asegurar que damos la mejor experiencia al usuario en nuestra web. Si sigues utilizando este sitio asumiremos que estás de acuerdo.", function (result) {
                if (result) {
                    window.location="$urlCookie";
                } else {
                    window.location="http://google.es";
                }
            });
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
        'message' => 'Utilizamos cookies propias y de terceros para obtener datos estadísticos de la navegación de nuestros usuarios y mejorar nuestros servicios. Si acepta o continúa navegando, consideramos que acepta su uso.',
        'btnOKClass' => 'btn-warning',
        'btnOKLabel' => 'Aceptar',
        'btnCancelClass' => 'btn-secondary',
        'btnCancelLabel' => 'Rechazar',
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

<div class="wrap">
    <?php
    NavBar::begin([
        // 'brandLabel' => Yii::$app->name,
        'brandLabel' => Html::img('@web/img/logo.png', ['class' => 'navbar-logo']),
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
                ['label' => 'Home', 'url' => ['/site/admin-index']],
                ['label' => 'Géneros', 'url' => ['/generos/index']],
                ['label' => 'Álbumes', 'url' => ['/albumes/index']],
                ['label' => 'Canciones', 'url' => ['/canciones/index']],
                ['label' => 'Usuarios', 'url' => ['/usuarios/index']],
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
                    ['label' => 'Home', 'url' => ['/site/index']],
                    ['label' => 'Álbumes', 'url' => ['/albumes/index']],
                    ['label' => 'Canciones', 'url' => ['/canciones/index']],
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
                                ['label' => 'Mi cuenta', 'url' => ['usuarios/perfil', 'id' => Yii::$app->user->id]],
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
        }
    } else {
        $items = [];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
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

<div class="full-player">
    <div class="info-song d-flex">
        <img alt="song-cover">
        <div class="mx-auto my-auto artist-info">
            <p class="m-0"></p>
            <small class="text-white"></small>
        </div>
    </div>
    <div class="player">
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
