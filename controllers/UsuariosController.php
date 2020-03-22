<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\Usuarios;
use app\models\UsuariosSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UsuariosController implements the CRUD actions for Usuarios model.
 */
class UsuariosController extends Controller
{
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
                'only' => ['index', 'update', 'create', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rules, $action) {
                            return Yii::$app->user->identity->login === 'admin'
                                && Yii::$app->user->identity->rol === 'admin';
                        }
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

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
    public function actionMail($email, $body)
    {
        return Yii::$app->mailer->compose()
                ->setFrom(Yii::$app->params['smtpUsername'])
                ->setTo($email)
                ->setSubject('Mensaje de confirmación para Mus!c Now')
                ->setHtmlBody($body)
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
                if ($loginModel->getUser() !== null) {
                    if ($loginModel->getUser()->confirm_token !== null) {
                        Yii::$app->session->setFlash('warning', 'Debes confirmar el correo.');
                    } else {
                        if ($loginModel->login()) {
                            return $this->goBack();
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

                $body = <<<EOT
                    <h2>Pulsa el siguiente enlace para confirmar la cuenta de correo.<h2>
                    <a href="$url">Confirmar cuenta</a>
                EOT;

                if ($this->actionMail($userModel->email, $body)) {
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
            'action' => $action
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
}
