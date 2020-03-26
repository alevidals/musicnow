<?php

namespace app\controllers;

use app\models\Canciones;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Usuarios;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'index'],
                'rules' => [
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {

        $canciones = Canciones::find('album a')->all();
        $usuario = Usuarios::findOne(['id' => Yii::$app->user->id]);

        if (($cadena = Yii::$app->request->get('cadena', ''))) {
            return $this->redirect(['site/search', 'cadena' => $cadena]);
        }

        return $this->render('index', [
            'canciones' => $canciones,
            'usuario' => $usuario,
            'cadena' => $cadena,
        ]);
    }

    public function actionSearch($cadena)
    {

        $usuariosSearch = new ActiveDataProvider([
            'query' => Usuarios::find()
                ->where(['ilike', 'login', $cadena])
                ->orWhere(['ilike', 'email', $cadena]),
        ]);

        $ids = [];
        $usuarios = Usuarios::find()->select('id')->where(['ilike', 'login', $cadena])->all();

        foreach ($usuarios as $usuario) {
            $ids[] = $usuario->id;
        }

        $cancionesSearch = new ActiveDataProvider([
            'query' => Canciones::find()
                ->where(['ilike', 'titulo', $cadena])
                ->orWhere(['IN', 'usuario_id', $ids]),
        ]);

        return $this->render('search', [
            'cadena' => $cadena,
            'usuariosSearch' => $usuariosSearch,
            'cancionesSearch' => $cancionesSearch,
        ]);

    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
