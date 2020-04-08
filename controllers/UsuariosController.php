<?php

namespace app\controllers;

use app\models\Bloqueados;
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
    public function actionMail($email, $url)
    {
        return Yii::$app->mailer->compose('layouts/confirm-mail', ['content' => $url])
                ->setFrom(Yii::$app->params['smtpUsername'])
                ->setTo($email)
                ->setSubject('Mensaje de confirmación para Mus!c Now')
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
            Yii::$app->session->setFlash('success', 'Correo confirmado. Inicie sesión');
            return $this->redirect(['usuarios/login']);
        }
        Yii::$app->session->setFlash('error', 'El correo no se ha podido confirmar.');
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
                        Yii::$app->session->setFlash('warning', 'Debes confirmar el correo.');
                    } elseif ($user->deleted_at !== null) {
                        Yii::$app->session->setFlash('error', 'La cuenta de este usuario está eliminada.');
                    } else {
                        if ($loginModel->login()) {
                            Yii::debug($user);
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

                if ($this->actionMail($userModel->email, $url)) {
                    Yii::$app->session->setFlash('success', 'Se ha enviado un correo a su email. Por favor confirme su cuenta.');
                } else {
                    Yii::$app->session->setFlash('error', 'No se ha podido mandar el correo, inténtelo más tarde.');
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
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionPerfil($id)
    {
        $model = Usuarios::findOne($id);

        if ($model === null) {
            Yii::$app->session->setFlash('error', 'El usuario no existe');
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
                Yii::$app->session->setFlash('error', 'Se ha producido un error interno.');
            }
        } else {
            return $this->redirect(['site/index']);
        }
    }
}
