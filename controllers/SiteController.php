<?php

namespace app\controllers;

use app\models\Canciones;
use app\models\ContactForm;
use app\models\Usuarios;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

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
                'only' => ['index', 'logout', 'admin-index'],
                'rules' => [
                    [
                        'actions' => ['admin-index'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rules, $action) {
                            return Yii::$app->user->identity->login === 'admin'
                                && Yii::$app->user->identity->rol === 1;
                        },
                    ],
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
        $usuario = Usuarios::findOne(['id' => Yii::$app->user->id]);

        $ids = $usuario->getSeguidos()->select('id')->column();

        $canciones = Canciones::find()->where(['IN', 'usuario_id', $ids])->orWhere(['usuario_id' => Yii::$app->user->id])->all();

        if (($cadena = Yii::$app->request->get('cadena', ''))) {
            return $this->redirect(['site/search', 'cadena' => $cadena]);
        }

        $likes = $usuario
            ->getLikes()
            ->select('cancion_id')
            ->column();

        return $this->render('index', [
            'canciones' => $canciones,
            'usuario' => $usuario,
            'likes' => $likes,
            'cadena' => $cadena,
        ]);
    }

    public function actionAdminIndex()
    {
        return $this->render('admin-index');
    }

    public function actionSearch($cadena)
    {
        $usuariosSearch = new ActiveDataProvider([
            'query' => Usuarios::find()
                ->where(['ilike', 'login', $cadena])
                ->orWhere(['ilike', 'email', $cadena])
                ->andWhere(['!=', 'rol', 1]),
        ]);

        $userIds = Usuarios::find()
            ->select('id')
            ->where(['ilike', 'login', $cadena])
            ->andWhere(['!=', 'rol', 1])
            ->column();

        $adminIds = Usuarios::find()
            ->select('id')
            ->where(['ilike', 'login', $cadena])
            ->andWhere(['=', 'rol', 1])
            ->column();

        $cancionesSearch = new ActiveDataProvider([
            'query' => Canciones::findWithTotalLikes()
                ->joinWith('usuario u')
                ->joinWith('genero g')
                ->where(['ilike', 'titulo', $cadena])
                ->orWhere(['IN', 'canciones.usuario_id', $userIds])
                ->andWhere(['NOT IN', 'canciones.usuario_id', $adminIds])
                ->addGroupBy(['g.denominacion', 'u.login']),
            'sort' => [
                'attributes' => [
                    'u.login',
                    'g.denominacion',
                    'likes',
                ],
            ],
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

    public function actionCookie()
    {
        setcookie('cookie-accept', 'true', time() + 3600 * 24 * 30);
        $this->goBack();
    }

    public function actionIdioma($lang)
    {
        setcookie('lang', $lang, time() + 3600 * 24 * 30);
        return $this->redirect(['site/index']);
    }
}
