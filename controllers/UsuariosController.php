<?php

namespace app\controllers;

use app\models\Bloqueados;
use app\models\Chat;
use app\models\Estados;
use app\models\LoginForm;
use app\models\Usuarios;
use app\models\UsuariosSearch;
use DateTime;
use Yii;
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
                'only' => ['index', 'update', 'create', 'delete', 'imagen', 'eliminar-cuenta'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rules, $action) {
                            $id = Yii::$app->request->get('id');
                            return Yii::$app->user->identity->rol === 1
                                || ($id == Yii::$app->user->id);
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
        $model = new Usuarios();

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
        $model->uploadImg(true);

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
        $this->findModel($id)->delete();

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
                    if ($user->confirm_token !== null) {
                        Yii::$app->session->setFlash('warning', Yii::t('app', 'YouHaveToConfirm'));
                    } elseif ($user->deleted_at !== null) {
                        Yii::$app->session->setFlash('error', Yii::t('app', 'DeletedAccount'));
                    } else {
                        if ($loginModel->login()) {
                            $user->estado_id = 2;
                            $user->save();
                            if ($user->rol == 1) {
                                return $this->redirect(['site/admin-index']);
                            } else {
                                return $this->redirect(['site/index']);
                            }
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

        $canciones_id = $model->getCanciones()->select('id')->column();
        $canciones = $model->getCanciones()->all();
        $albumes = $model->getAlbumes()->all();
        $seguidores = $model->getSeguidores()->all();
        $seguidos = $model->getSeguidos()->all();
        $likes = Usuarios::findOne(Yii::$app->user->id)
            ->getLikes()
            ->select('cancion_id')
            ->where(['IN', 'cancion_id', $canciones_id])
            ->column();

        return $this->render('perfil', [
            'model' => $model,
            'canciones' => $canciones,
            'albumes' => $albumes,
            'seguidores' => $seguidores,
            'seguidos' => $seguidos,
            'likes' => $likes,
            'bloqueo' => $bloqueo,
        ]);
    }

    public function actionEliminarImagen($id)
    {
        $model = $this->findModel($id);
        $model->url_image = Yii::$app->params['defaultImgProfile'];
        if ($model->save()) {
            return $this->goBack();
        }
    }

    public function actionEliminarCuenta($id)
    {
        $model = $this->findModel($id);

        if ($model) {
            $model->deleted_at = (new Datetime())->format('Y-m-d H:i:s');
            if ($model->save()) {
                return $this->redirect(['usuarios/logout']);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'InternalError'));
            }
        } else {
            return $this->redirect(['site/index']);
        }
    }

    public function actionEstados($id)
    {
        $usuario = Usuarios::findOne($id);
        $seguidos = $usuario->getSeguidos()->all();

        foreach ($seguidos as &$seguido) {
            $seguido['estado_id'] = Estados::findOne($seguido['estado_id'])->estado;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $seguidos;
    }

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
                ->select(['mensaje', 'c.created_at', 'url_image', 'login'])
                ->from('chat c')
                ->leftJoin('usuarios', 'usuarios.id = c.emisor_id')
                ->where(['c.estado_id' => 3])
                ->andWhere(['receptor_id' => $usuario->id])
                ->orderBy(['c.created_at' => SORT_DESC]);

            $mensajes->limit($mensajes->count() - $total);
            $res['mensajes'] = $mensajes->all();
            $res['count'] = $mensajes->count();
        }

        return $res;
    }

    public function actionSendResetPass()
    {
        $model = new Usuarios();

        if (Yii::$app->request->post('Usuarios')['email']) {
            $email = Yii::$app->request->post('Usuarios')['email'];
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
            'model' => $model
        ]);
    }

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
            'model' => $model
        ]);
    }
}
