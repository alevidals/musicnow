<?php

namespace app\controllers;

use app\models\Albumes;
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
                            return Yii::$app->user->identity->rol_id === 1;
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

        $canciones = Canciones::find()
            ->where(['IN', 'usuario_id', $ids])
            ->orWhere(['usuario_id' => Yii::$app->user->id])
            ->limit(10)
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $usuariosSearch = new ActiveDataProvider([
            'query' => Usuarios::find()->where('1=0'),
        ]);

        $cancionesSearch = new ActiveDataProvider([
            'query' => Canciones::find()->where('1=0'),
        ]);

        $albumesSerch = new ActiveDataProvider([
            'query' => Albumes::find()->where('1=0'),
        ]);

        if (($cadena = Yii::$app->request->get('cadena', ''))) {
            $usuariosSearch = new ActiveDataProvider([
                'query' => Usuarios::find()
                    ->where(['ilike', 'login', $cadena])
                    ->orWhere(['ilike', 'email', $cadena])
                    ->andWhere(['!=', 'rol_id', 1]),
            ]);

            $albumesSerch = new ActiveDataProvider([
                'query' => Albumes::find()
                    ->where(['ilike', 'titulo', $cadena])
            ]);

            $userIds = Usuarios::find()
                ->select('id')
                ->where(['ilike', 'login', $cadena])
                ->andWhere(['!=', 'rol_id', 1])
                ->column();

            $adminIds = Usuarios::find()
                ->select('id')
                ->where(['ilike', 'login', $cadena])
                ->andWhere(['=', 'rol_id', 1])
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
            'usuariosSearch' => $usuariosSearch,
            'cancionesSearch' => $cancionesSearch,
            'albumesSearch' => $albumesSerch,
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
                ->andWhere(['!=', 'rol_id', 1]),
        ]);

        $userIds = Usuarios::find()
            ->select('id')
            ->where(['ilike', 'login', $cadena])
            ->andWhere(['!=', 'rol_id', 1])
            ->column();

        $adminIds = Usuarios::find()
            ->select('id')
            ->where(['ilike', 'login', $cadena])
            ->andWhere(['=', 'rol_id', 1])
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

    public function actionGetTranslate()
    {
        $strings = Yii::$app->request->get('strings');

        foreach ($strings as &$string) {
            $string = Yii::t('app', $string);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $strings;
    }

    public function actionGetMorePosts($offset)
    {
        $usuario = Usuarios::findOne(['id' => Yii::$app->user->id]);

        $ids = $usuario->getSeguidos()->select('id')->column();


        $canciones = (new \yii\db\Query())
            ->select(['c.*', 'u.login', 'u.url_image', 'a.titulo as album_titulo'])
            ->from('canciones c')
            ->leftJoin('usuarios u', 'u.id = c.usuario_id')
            ->leftJoin('albumes a', 'c.album_id = a.id')
            ->where(['IN', 'c.usuario_id', $ids])
            ->orWhere(['c.usuario_id' => Yii::$app->user->id])
            ->offset($offset)
            ->limit(10)
            ->orderBy(['c.created_at' => SORT_DESC])
            ->all();

        $likes = $usuario
            ->getLikes()
            ->select('cancion_id')
            ->column();

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'canciones' => $canciones,
            'usuario' => $usuario,
            'likes' => $likes,
        ];
    }
}
