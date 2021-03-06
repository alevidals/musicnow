<?php

namespace app\controllers;

use app\models\Bloqueados;
use app\models\CancionesPlaylist;
use app\models\CancionesPlaylistSearch;
use app\models\Chat;
use app\models\Estados;
use app\models\LoginForm;
use app\models\Seguidores;
use app\models\Usuarios;
use app\models\UsuariosSearch;
use app\services\Utility;
use DateTime;
use Yii;
use yii\bootstrap4\Html;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * UsuariosController implements the CRUD actions for Usuarios model.
 */
class UsuariosController extends Controller
{
    const YOU_BLOCK = 1;
    const OTHER_BLOCK = 2;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'update', 'create', 'delete', 'eliminar-cuenta'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'delete', 'update'],
                        'roles' => ['@'],
                        'matchCallback' => function ($rules, $action) {
                            return Yii::$app->user->identity->rol_id === 1;
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'eliminar-cuenta'],
                        'roles' => ['@'],
                        'matchCallback' => function ($rules, $action) {
                            $id = Yii::$app->request->get('id');
                            return $id == Yii::$app->user->id;
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Usuarios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsuariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Usuarios model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Usuarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Usuarios(['scenario' => Usuarios::SCENARIO_CREAR]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Usuarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->scenario = Usuarios::SCENARIO_UPDATE;

        $model->image = UploadedFile::getInstance($model, 'image');
        $model->uploadImg(Utility::PERFIL);

        $model->banner = UploadedFile::getInstance($model, 'banner');
        $model->uploadImg(Utility::BANNER);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['perfil', 'id' => $model->id]);
        }

        $model->password = '';
        $model->password_repeat = '';


        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Usuarios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $datosCanciones = $model->getCanciones()->all();
        $datosAlbumes = $model->getAlbumes()->all();
        $canciones = [];
        $portadas = [];
        $albumes = [];

        foreach ($datosCanciones as $cancion) {
            $canciones[] = $cancion->song_name;
            if ($cancion->album_id == null) {
                $portadas[] = $cancion->image_name;
            }
        }

        foreach ($datosAlbumes as $album) {
            $albumes[] = $album->image_name;
        }

        if ($model->delete()) {
            if ($model->image_name != null) {
                $model->deleteImage();
            }

            if ($model->banner_name != null) {
                $model->deleteBanner();
            }

            foreach ($canciones as $data) {
                Utility::deleteFileFirebase('canciones/' . $id . '/' . $data);
            }

            foreach ($portadas as $data) {
                Utility::deleteFileFirebase('images/portada/' . $id . '/' . $data);
            }

            foreach ($albumes as $data) {
                Utility::deleteFileFirebase('images/portada/' . $id . '/' . $data);
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Usuarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Usuarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Usuarios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Envía un correo a a una dirección específica con el cuerpo
     * del mensaje pasado por parámetros.
     *
     * @param string $email dirección a la que queremos mandar el correo
     * @param string $body cuerpo que se enviará al correo
     * @param mixed $url
     * @param mixed $layout
     * @return bool  true si se envía o false si hay error
     */
    public function actionMail($email, $url, $layout)
    {
        return Yii::$app->mailer->compose('layouts/' . $layout, ['content' => $url])
                ->setFrom(Yii::$app->params['smtpUsername'])
                ->setTo($email)
                ->setSubject(Yii::t('app', 'ConfirmMailSubject'))
                ->send();
    }

    /**
     * Confirma la cuenta un usuario.
     *
     * @param int $id el id del usuario al que se le confirmará la cuenta
     * @param string $confirm_token token de confirmación que se eliminará
     */
    public function actionActivar($id, $confirm_token)
    {
        $model = $this->findModel($id);
        if ($model->confirm_token === $confirm_token) {
            $model->confirm_token = null;
            $model->save();
            Yii::$app->session->setFlash('success', Yii::t('app', 'ConfirmedMail'));
            return $this->redirect(['usuarios/login']);
        }
        Yii::$app->session->setFlash('error', Yii::t('app', 'CannotConfirm'));
        return $this->redirect(['site/index']);
    }

    /**
     * Reenvia el mensaje de confirmación al correo de la cuenta.
     *
     * @param string $email email de la cuenta del usuario
     * @param int $id id del usuario
     * @param string $confirm_token token de confirmación
     * @return mixed
     */
    public function actionResendConfirmMail($email, $id, $confirm_token)
    {
        $url = Url::to([
            'usuarios/activar',
            'id' => $id,
            'confirm_token' => $confirm_token,
        ], true);

        if ($this->actionMail($email, $url, 'confirm-mail')) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'ConfirmMail'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ErrorMail'));
        }

        return $this->goBack();
    }

    /**
     * Autentifica al usuario si envía el formulario de login o lo
     * registra si envía el formulario de registro.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['site/index']);
        }

        $loginModel = new LoginForm();
        $userModel = new Usuarios(['scenario' => Usuarios::SCENARIO_CREAR]);
        $action = 'login';

        if (isset($_POST['LoginForm'])) {
            $action = 'login';
            if ($loginModel->load(Yii::$app->request->post())) {
                $user = $loginModel->getUser();
                if ($user !== null) {
                    if ($user->deleted_at !== null) {
                        $url = Url::to(
                            ['usuarios/enviar-email-recuperacion', 'id' => $user->id],
                            true
                        );
                        Yii::$app->session->setFlash('error', Yii::t('app', 'DeletedAccount') . ' ' . Html::a(Yii::t('app', 'Recover'), $url, ['class' => 'normal-link']));
                    } elseif ($user->confirm_token !== null) {
                        $url = Url::to([
                            'usuarios/resend-confirm-mail',
                            'email' => $user->email,
                            'id' => $user->id,
                            'confirm_token' => $user->confirm_token,
                        ]);
                        Yii::$app->session->setFlash('warning', Yii::t('app', 'YouHaveToConfirm') . ' ' . Html::a(Yii::t('app', 'Resend'), $url, ['class' => 'normal-link']));
                    } else {
                        if ($loginModel->login()) {
                            $user->estado_id = 2;
                            $user->save();
                            if ($user->rol_id == 1) {
                                return $this->redirect(['site/admin-index']);
                            }
                            return $this->redirect(['site/index']);
                        }
                    }
                }
            }
        }

        if (isset($_POST['Usuarios'])) {
            $action = 'register';
            if ($userModel->load(Yii::$app->request->post()) && $userModel->save()) {
                $url = Url::to([
                    'usuarios/activar',
                    'id' => $userModel->id,
                    'confirm_token' => $userModel->confirm_token,
                ], true);

                if ($this->actionMail($userModel->email, $url, 'confirm-mail')) {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'ConfirmMail'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'ErrorMail'));
                }

                return $this->redirect(['usuarios/login']);
            }
        }

        $userModel->password = '';
        $userModel->password_repeat = '';
        return $this->render('login', [
            'loginFormModel' => $loginModel,
            'userModel' => $userModel,
            'action' => $action,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        $model = $this->findModel(Yii::$app->user->id);
        $model->estado_id = 1;
        $model->save();

        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Renderiza el perfil del usuario indicado.
     *
     * @param int $id el id del usuario a mostrar
     */
    public function actionPerfil($id)
    {
        $model = Usuarios::findOne($id);

        if ($model === null) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'UserNotFound'));
            return $this->redirect(['site/index']);
        }

        $bloqueo = Bloqueados::findOne(['bloqueador_id' => $id, 'bloqueado_id' => Yii::$app->user->id]);

        if ($bloqueo !== null) {
            $bloqueo = self::OTHER_BLOCK;
        } else {
            $bloqueo = Bloqueados::findOne(['bloqueador_id' => Yii::$app->user->id, 'bloqueado_id' => $id]);
            if ($bloqueo !== null) {
                $bloqueo = self::YOU_BLOCK;
            }
        }

        $videoclips = $model->getVideoclips()->all();

        $canciones_id = $model->getCanciones()->select('id')->column();
        $canciones = $model->getCanciones();
        $cancionesIds = $model->getCanciones()
            ->distinct()
            ->select('album_id')
            ->column();
        $albumes = $model->getAlbumes()->where(['in', 'id', $cancionesIds])->all();
        $seguidores = $model->getSeguidores()->all();
        $seguidos = $model->getSeguidos()->all();
        $likes = Usuarios::findOne(Yii::$app->user->id)
            ->getLikes()
            ->select('cancion_id')
            ->where(['IN', 'cancion_id', $canciones_id])
            ->column();
        $playlistsWithSongsIds = CancionesPlaylist::find()->select('playlist_id')->column();
        $playlists = $model->getPlaylists()->where(['in', 'id', $playlistsWithSongsIds]);

        return $this->render('perfil', [
            'model' => $model,
            'canciones' => $canciones,
            'albumes' => $albumes,
            'seguidores' => $seguidores,
            'seguidos' => $seguidos,
            'likes' => $likes,
            'bloqueo' => $bloqueo,
            'videoclips' => $videoclips,
            'playlists' => $playlists->all(),
        ]);
    }

    /**
     * Elimina la imagen de perfil del usuario y pasa a usar
     * la imagen por defecto.
     *
     * @param int $id el id del usuario al que se le eliminará
     * la imagen de perfil
     * @return mixed
     */
    public function actionEliminarImagen($id)
    {
        $model = $this->findModel($id);
        if ($model->image_name != null) {
            $model->deleteImage();
        }
        $model->url_image = Yii::getAlias('@web/img/user-profile.png');
        $model->image_name = null;
        if ($model->save()) {
            return $this->redirect(['usuarios/configurar']);
        }
    }

    /**
     * Elimina el banner de perfil del usuario.
     *
     * @param int $id el id del usuario al que se le eliminará
     * el banner
     * @return mixed
     */
    public function actionEliminarBanner($id)
    {
        $model = $this->findModel($id);
        $model->deleteBanner();
        $model->url_banner = null;
        $model->banner_name = null;
        if ($model->save()) {
            return $this->redirect(['usuarios/configurar']);
        }
    }

    /**
     * Deshabilita la cuenta 30 días, si en 30 días no la recupera
     * la cuenta se eliminará
     *
     * @param int $id
     * @return mixed
     */
    public function actionEliminarCuenta($id)
    {
        $model = $this->findModel($id);

        if ($model) {
            $model->deleted_at = (new Datetime())->format('Y-m-d H:i:s');
            if ($model->save()) {
                return $this->redirect(['usuarios/logout']);
            }
            Yii::$app->session->setFlash('error', Yii::t('app', 'InternalError'));
        } else {
            return $this->redirect(['site/index']);
        }
    }

    /**
     * Devuelve los estados de los usuarios a los que el usuario
     * sigue.
     *
     * @return array
     */
    public function actionEstados()
    {
        $usuario = Usuarios::findOne(Yii::$app->user->id);
        $seguidos = $usuario->getSeguidos()->all();

        foreach ($seguidos as &$seguido) {
            $seguido['estado_id'] = Estados::findOne($seguido['estado_id'])->estado;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $seguidos;
    }

    /**
     * Devuelve los mensajes no leídos del usuario conectado.
     *
     * @param int $receptor_id el id del receptor de los mensajes
     * @param bool $total total de mensajes para así limitar la consulta
     * @return array
     */
    public function actionGetNoReadMessages($receptor_id = null, $total = null)
    {
        $usuario = Usuarios::findOne(Yii::$app->user->id);

        if ($receptor_id) {
            $res = Chat::find()
                ->where(['emisor_id' => $receptor_id])
                ->andWhere(['receptor_id' => $usuario->id])
                ->andWhere(['estado_id' => 3])
                ->count();
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $mensajes = (new \yii\db\Query())
                ->select(['c.id', 'mensaje', 'c.created_at', 'url_image', 'login'])
                ->from('chat c')
                ->leftJoin('usuarios', 'usuarios.id = c.emisor_id')
                ->where(['c.estado_id' => 3])
                ->andWhere(['notified' => false])
                ->andWhere(['receptor_id' => $usuario->id])
                ->orderBy(['c.created_at' => SORT_DESC]);

            $mensajes->limit($mensajes->count() - $total);
            $mensajesEncoded = $mensajes->all();
            foreach ($mensajesEncoded as &$mensaje) {
                $mensaje['mensaje'] = Html::encode($mensaje['mensaje']);
                $mensaje['login'] = Html::encode($mensaje['login']);
            }
            $res['mensajes'] = $mensajesEncoded;
            $res['count'] = $mensajes->count();
        }

        return $res;
    }

    /**
     * Método que envía un correo para poder restablecer la contraseña.
     *
     * @return mixed
     */
    public function actionSendResetPass()
    {
        $model = new Usuarios();

        $post = Yii::$app->request->post('Usuarios');

        if ($post !== null && $post['email']) {
            $email = $post['email'];
            $model = Usuarios::findOne(['email' => $email]);
            if ($model) {
                $url = Url::to([
                    'usuarios/reset-pass',
                    'id' => $model->id,
                ], true);
                if ($this->actionMail($model->email, $url, 'reset-pass-mail')) {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'pass-mail-send'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'ErrorMail'));
                }
                return $this->redirect(['usuarios/login']);
            }
        }

        return $this->render('reset-pass-email', [
            'model' => $model,
        ]);
    }

    /**
     * Restablece la contraseña del usuario indicado.
     *
     * @param int $id el id del usuario al que se le va a restablecer
     * la cuenta
     * @return mixed
     */
    public function actionResetPass($id)
    {
        $model = Usuarios::findOne($id);
        $model->scenario = Usuarios::SCENARIO_UPDATE;
        $model->password = '';
        $model->password_repeat = '';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'pass-changed'));
            return $this->redirect(['usuarios/login']);
        }

        return $this->render('reset-pass-form', [
            'model' => $model,
        ]);
    }

    /**
     * Devuelve el número de nuevos seguidores.
     *
     * @param int $total el total de seguidores antes de hacer la
     * consulta para así limitarla
     * @return array
     */
    public function actionGetNewFollowers($total)
    {
        $res = [];

        $seguidores = (new \yii\db\Query())
            ->select(['usuarios.login', 'url_image', 'usuarios.id'])
            ->from('seguidores s')
            ->leftJoin('usuarios', 's.seguidor_id = usuarios.id')
            ->where(['seguido_id' => Yii::$app->user->id])
            ->orderBy(['s.id' => SORT_DESC]);

        $seguidoresEncoded = $seguidores
            ->limit($seguidores->count() - $total)
            ->all();

        foreach ($seguidoresEncoded as &$seguidor) {
            $seguidor['login'] = Html::encode($seguidor['login']);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $res['seguidores'] = $seguidoresEncoded;
        $res['count'] = $seguidores->count();

        return $res;
    }

    /**
     * Devuelve el número de seguidores del usuario autenticado.
     *
     * @return int
     */
    public function actionGetFollowersData()
    {
        $model = Usuarios::findOne(Yii::$app->user->id);
        return $model->getSeguidores()->count();
    }

    /**
     * Método que envía un email para recuperar la cuenta si
     * anteriormente la ha eliminado y sigue deshabilitada.
     *
     * @param int $id el id del usuario al que se le enviará el email
     * @return mixed
     */
    public function actionEnviarEmailRecuperacion($id)
    {
        $model = Usuarios::findOne($id);
        $model->confirm_token = Yii::$app->security->generateRandomString(255);
        $model->save();

        $url = Url::to([
            'usuarios/recuperar',
            'id' => $model->id,
            'confirm_token' => $model->confirm_token,
        ], true);

        $this->actionMail($model->email, $url, 'confirm-restore-account');

        Yii::$app->session->setFlash('success', Yii::t('app', 'EmailSended'));

        return $this->goBack();
    }

    /**
     * Recupera la cuenta del usuario volviéndola a activar.
     *
     * @param int $id el id del usuario al que se le activará la cuenta
     * @param string $confirm_token token de confirmación
     * @return mixed
     */
    public function actionRecuperar($id, $confirm_token)
    {
        $model = $this->findModel($id);
        if ($model->confirm_token === $confirm_token) {
            $model->confirm_token = null;
            $model->deleted_at = null;
            $model->save();
            Yii::$app->session->setFlash('success', Yii::t('app', 'RecoveredAccount'));
            return $this->redirect(['usuarios/login']);
        }
        Yii::$app->session->setFlash('error', Yii::t('app', 'CannotRecover'));
        return $this->redirect(['site/index']);
    }

    /**
     * Devuelve las playlists del usuario indicado.
     *
     * @param int $usuario_id el id del usuario del que se desea
     * conocer las playlists
     * @param mixed $cancion_id
     * @return array
     */
    public function actionGetPlaylists($usuario_id, $cancion_id)
    {
        $usuario = Usuarios::findOne($usuario_id);

        $playlistsIds = CancionesPlaylistSearch::find()
            ->select('playlist_id')
            ->where(['=', 'cancion_id', $cancion_id])
            ->column();

        $playlists = $usuario
            ->getPlaylists()
            ->where(['NOT IN', 'id', $playlistsIds])
            ->all();
        $playlistsPortadas = [];

        foreach ($playlists as $playlist) {
            $playlist['titulo'] = Html::encode($playlist['titulo']);
            if ($playlist->getCanciones()->count() > 0) {
                $randomNumber = rand(0, $playlist->getCanciones()->count() - 1);
                $playlistsPortadas[$playlist->id] = $playlist->getCanciones()->all()[$randomNumber]->url_portada;
            } else {
                $playlistsPortadas[$playlist->id] = Yii::getAlias('@web/img/playlists.png');
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'playlists' => $playlists,
            'playlistsPortadas' => $playlistsPortadas,
        ];
    }

    /**
     * Renderiza la vista de las notificaciones del usuario conectado.
     *
     * @return mixed
     */
    public function actionNotificaciones()
    {
        $model = Usuarios::findOne(Yii::$app->user->id);
        $solicitudes = $model->getSolicitudesSeguimientos()->all();

        $notificaciones = [];

        foreach ($solicitudes as $solicitud) {
            $usuarioSolicitud = Usuarios::findOne($solicitud->seguidor_id);
            array_push($notificaciones, [
                'url_image' => $usuarioSolicitud->url_image,
                'login' => $usuarioSolicitud->login,
                'id' => $usuarioSolicitud->id,
            ]);
        }

        return $this->render('notificaciones', [
            'model' => $model,
            'notificaciones' => $notificaciones,
        ]);
    }

    /**
     * Renderiza la vista de la configuración del perfil.
     *
     * @return mixed
     */
    public function actionConfigurar()
    {
        $model = Usuarios::findOne(Yii::$app->user->id);

        $model->scenario = Usuarios::SCENARIO_UPDATE;

        $model->password = '';
        $model->password_repeat = '';


        return $this->render('configurar', [
            'model' => $model,
        ]);
    }

    /**
     * Acción que se encarga de comprobar si el usuario autenticado es
     * premium
     *
     * @return bool true si es premium y en caso contrario false
     */
    public function actionEsPremium()
    {
        $model = Usuarios::findOne(Yii::$app->user->id);

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ($model->rol_id == 3) ? true : false;
    }
}
