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
use yii\widgets\Pjax;

AppAsset::register($this);

$isLogued = !Yii::$app->user->isGuest;

$js = <<<EOT
    if ('$isLogued') {
        getPremium();
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
<?php (!Yii::$app->user->isGuest && Yii::$app->user->identity->rol_id != 1) ? Pjax::begin() : ''?>
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
                    ['label' => Yii::t('app', 'Tendencias'), 'url' => ['/site/tendencias'], 'options' => ['class' => 'my-auto']],
                    ['label' => 'Chat<span class="badge badge-warning ml-1 messages-number"></span>', 'url' => ['/chat/chat'], 'options' => ['class' => 'my-auto']],
                    ['label' => 'Notificaciones<span class="badge badge-warning ml-1 notifications-number">', 'url' => ['/usuarios/notificaciones'], 'options' => ['class' => 'my-auto']],
                    [
                        'label' => Yii::t('app', 'Manage'),
                        'options' => ['class' => 'my-auto'],
                        'items' => [
                            ['label' => Yii::t('app', 'Albumes'), 'url' => ['/albumes/index'], 'linkOptions' => ['data-pjax' => 0]],
                            ['label' => Yii::t('app', 'Canciones'), 'url' => ['/canciones/index'], 'linkOptions' => ['data-pjax' => 0]],
                            ['label' => Yii::t('app', 'Playlists'), 'url' => ['/playlists/index'], 'linkOptions' => ['data-pjax' => 0]],
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
                                ['label' => Yii::t('app', 'ConfigureProfile'), 'url' => ['usuarios/configurar'], 'linkOptions' => ['data-pjax' => 0]],
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

<?php (!Yii::$app->user->isGuest && Yii::$app->user->identity->rol_id != 1) ? Pjax::end() : ''; ?>

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
            <em class="fas fa-chevron-left"></em>
        </button>
    </div>
    <div class="full-player w-100 row ml-0">
        <div class="info-song col-12 col-lg-4 col-xl-3 ml-0 row">
            <img class="height-60" src="https://firebasestorage.googleapis.com/v0/b/jiejie-test.appspot.com/o/user-profile.png?alt=media&token=6b233a2d-1bf7-41bf-9475-e43838896fa0" alt="song-cover col-2">
                <button class="my-auto ml-2 action-btn backward-btn outline-transparent"><em class="fas fa-backward"></em></button>
                <div class="artist-info my-auto col text-center text-truncate">
                    <p class="m-0"></p>
                    <small></small>
                </div>
                <button class="my-auto mr-2 action-btn forward-btn outline-transparent"><em class="fas fa-forward"></em></button>
        </div>
        <div class="player col-12 col-lg-8 col-xl-9">
            <audio id="audio">
                <source src="https://firebasestorage.googleapis.com/v0/b/jiejie-test.appspot.com/o/notificacion.mp3?alt=media&token=fe228a37-f5fe-402b-9bcb-7ab747d24b34">
            </audio>
        </div>
    </div>
</div>

<button class="scroll-top-btn outline-transparent"><em class="fas fa-arrow-circle-up"></em></button>


<footer class="footer" style="height: fit-content;">
    <div class="container mx-auto row text-center">
        <p class="col-12 p-0 m-0 col-lg">&copy; Mus!c Now <?= date('Y') ?></p>
        <div class="col social-media">
            <a class="action-btn mx-2" href="https://www.instagram.com/musicnow">
                <em class="fab fa-instagram"></em>
            </a>
            <a class="action-btn mx-2" href="https://www.twitter.com/musicnow">
                <em class="fab fa-twitter"></em>
            </a>
            <a class="action-btn mx-2" href="https://www.youtube.com/musicnow">
                <em class="fab fa-youtube"></em>
            </a>
            <a class="action-btn mx-2" href="https://www.facebook.com/musicnow">
                <em class="fab fa-facebook"></em>
            </a>
        </div>
        <div class="col-12 col-lg">
            <em class="fas fa-sun"></em>
            <div class="custom-control custom-switch d-inline-block">
                <input type="checkbox" class="custom-control-input " id="darkSwitch">
                <label class="custom-control-label" for="darkSwitch"><em class="fas fa-moon"></em><span class="d-none">moon</span></label>
            </div>
        </div>
        <div class="col-12 col-lg">
            <button type="button" class="policy-btn outline-transparent" data-toggle="modal" data-target="#policyModal">
                    <?= Yii::t('app', 'Policy') ?>
            </button>
        </div>
    </div>
</footer>

<div class="modal fade" id="policyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><?= Yii::t('app', 'Policy') ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body custom-overflow text-justify no-break-word">
            <p>El presente Política de Privacidad establece los términos en que Mus!c Now usa y protege la información que es
                proporcionada por sus usuarios al momento de utilizar su sitio web. Esta compañía está comprometida con la
                seguridad de los datos de sus usuarios. Cuando le pedimos llenar los campos de información personal con la cual
                usted pueda ser identificado, lo hacemos asegurando que sólo se empleará de acuerdo con los términos de este
                documento. Sin embargo esta Política de Privacidad puede cambiar con el tiempo o ser actualizada por lo que le
                recomendamos y enfatizamos revisar continuamente esta página para asegurarse que está de acuerdo con dichos
                cambios.</p>
            <p><strong>Información que es recogida</strong></p>
            <p>Nuestro sitio web podrá recoger información personal por ejemplo: Nombre,&nbsp; información de contacto
                como&nbsp; su dirección de correo electrónica e información demográfica. Así mismo cuando sea necesario podrá
                ser requerida información específica para procesar algún pedido o realizar una entrega o facturación.</p>
            <p><strong>Uso de la información recogida</strong></p>
            <p>Nuestro sitio web emplea la información con el fin de proporcionar el mejor servicio posible, particularmente
                para mantener un registro de usuarios, de pedidos en caso que aplique, y mejorar nuestros productos y servicios.
                &nbsp;Es posible que sean enviados correos electrónicos periódicamente a través de nuestro sitio con ofertas
                especiales, nuevos productos y otra información publicitaria que consideremos relevante para usted o que pueda
                brindarle algún beneficio, estos correos electrónicos serán enviados a la dirección que usted proporcione y
                podrán ser cancelados en cualquier momento.</p>
            <p>Mus!c Now está altamente comprometido para cumplir con el compromiso de mantener su información segura. Usamos
                los sistemas más avanzados y los actualizamos constantemente para asegurarnos que no exista ningún acceso no
                autorizado.</p>
            <p><strong>Cookies</strong></p>
            <p>Una cookie se refiere a un fichero que es enviado con la finalidad de solicitar permiso para almacenarse en su
                ordenador, al aceptar dicho fichero se crea y la cookie sirve entonces para tener información respecto al
                tráfico web, y también facilita las futuras visitas a una web recurrente. Otra función que tienen las cookies es
                que con ellas las web pueden reconocerte individualmente y por tanto brindarte el mejor servicio personalizado
                de su web.</p>
            <p>Nuestro sitio web emplea las cookies para poder identificar las páginas que son visitadas y su frecuencia. Esta
                información es empleada únicamente para análisis estadístico y después la información se elimina de forma
                permanente. Usted puede eliminar las cookies en cualquier momento desde su ordenador. Sin embargo las cookies
                ayudan a proporcionar un mejor servicio de los sitios web, estás no dan acceso a información de su ordenador ni
                de usted, a menos de que usted así lo quiera y la proporcione directamente, <a
                    href="https://embedinstagramfeed.com" target="_blank">visitas a una web </a>. Usted puede aceptar o negar el
                uso de cookies, sin embargo la mayoría de navegadores aceptan cookies automáticamente pues sirve para tener un
                mejor servicio web. También usted puede cambiar la configuración de su ordenador para declinar las cookies. Si
                se declinan es posible que no pueda utilizar algunos de nuestros servicios.</p>
            <p><strong>Enlaces a Terceros</strong></p>
            <p>Este sitio web pudiera contener en laces a otros sitios que pudieran ser de su interés. Una vez que usted de clic
                en estos enlaces y abandone nuestra página, ya no tenemos control sobre al sitio al que es redirigido y por lo
                tanto no somos responsables de los términos o privacidad ni de la protección de sus datos en esos otros sitios
                terceros. Dichos sitios están sujetos a sus propias políticas de privacidad por lo cual es recomendable que los
                consulte para confirmar que usted está de acuerdo con estas.</p>
            <p><strong>Control de su información personal</strong></p>
            <p>En cualquier momento usted puede restringir la recopilación o el uso de la información personal que es
                proporcionada a nuestro sitio web.&nbsp; Cada vez que se le solicite rellenar un formulario, como el de alta de
                usuario, puede marcar o desmarcar la opción de recibir información por correo electrónico. &nbsp;En caso de que
                haya marcado la opción de recibir nuestro boletín o publicidad usted puede cancelarla en cualquier momento.</p>
            <p>Esta compañía no venderá, cederá ni distribuirá la información personal que es recopilada sin su consentimiento,
                salvo que sea requerido por un juez con un orden judicial.</p>
            <p>Mus!c Now Se reserva el derecho de cambiar los términos de la presente Política de Privacidad en cualquier
                momento.</p>
            <p>Esta politica de privacidad se han generado en <a href="https://politicadeprivacidadplantilla.com/"
                    target="_blank">politicadeprivacidadplantilla.com</a>.<br></p>
        </div>
        </div>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
