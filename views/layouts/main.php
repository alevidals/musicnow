<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\widgets\Alert;
use kartik\dialog\Dialog;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);

$urlCookie = Url::to(['site/cookie']);

$js = <<<EOT
    $( document ).ready(function() {
        krajeeDialogCust2.confirm("Utilizamos cookies para asegurar que damos la mejor experiencia al usuario en nuestra web. Si sigues utilizando este sitio asumiremos que estás de acuerdo.", function (result) {
            if (result) {
                window.location="$urlCookie";
            } else {
                window.location="http://google.es";
            }
        });
    });
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

if (!isset($_COOKIE['cookie-accept'])) {
    $this->registerJS($js);
}


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
        'brandLabel' => Html::img('@web/img/logo.png', ['width' => '40px']),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-dark bg-dark navbar-expand-md fixed-top',
        ],
        'collapseOptions' => [
            'class' => 'justify-content-end',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index']],
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
        ],
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
