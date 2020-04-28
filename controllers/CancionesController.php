<?php

namespace app\controllers;

use app\models\Albumes;
use app\models\Canciones;
use app\models\CancionesSearch;
use app\models\Comentarios;
use app\models\Generos;
use app\models\Usuarios;
use app\services\Utility;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * CancionesController implements the CRUD actions for Canciones model.
 */
class CancionesController extends Controller
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
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Canciones models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CancionesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Canciones model.
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
     * Creates a new Canciones model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Canciones(['usuario_id' => Yii::$app->user->id]);

        if (Yii::$app->request->isPost) {
            if (isset($_POST['Canciones']['portada'])) {
                $model->portada = UploadedFile::getInstance($model, 'portada');
                $model->uploadPortada();
            }
            $model->cancion = UploadedFile::getInstance($model, 'cancion');
            $model->uploadCancion();
        }

        if ($model->load(Yii::$app->request->post())) {
            if (!isset($_POST['Canciones']['portada'])) {
                $model->url_portada = $model->getAlbum()->one()->url_portada;
                $model->image_name = $model->getAlbum()->one()->image_name;
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $usuario = Usuarios::findOne(Yii::$app->user->id);

        $albumes = $usuario->getAlbumes()->select('titulo')->indexBy('id')->column();

        return $this->render('create', [
            'model' => $model,
            'generos' => ['' => ''] + Generos::lista(),
            'albumes' => ['' => ''] + $albumes,
        ]);
    }

    /**
     * Updates an existing Canciones model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $model->portada = UploadedFile::getInstance($model, 'portada');
            if ($model->portada !== null) {
                $model->deletePortada();
                $model->uploadPortada();
            }
            $model->cancion = UploadedFile::getInstance($model, 'cancion');
            if ($model->cancion !== null) {
                $model->deleteCancion();
                $model->uploadCancion();
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $usuario = Usuarios::findOne(Yii::$app->user->id);
        $albumes = $usuario->getAlbumes()->select('titulo')->indexBy('id')->column();

        return $this->render('update', [
            'model' => $model,
            'generos' => ['' => ''] + Generos::lista(),
            'albumes' => ['' => ''] + $albumes,
        ]);
    }

    /**
     * Deletes an existing Canciones model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->delete();
        if ($model->album_id == null) {
            $model->deletePortada();
        }
        $model->deleteCancion();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Canciones model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Canciones the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Canciones::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionGetSongData($cancion_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($cancion_id);
        $res = [
            'url_cancion' => $model->url_cancion,
            'url_portada' => $model->url_portada,
            'titulo' => Html::encode($model->titulo),
            'explicit' => ($model->explicit) ? true : false,
            'message' => Yii::t('app', 'AddedToQueue')
        ];

        if ($model->album != null) {
            $album = $model->getAlbum()->one()->titulo;
            $res['album'] = Html::encode($album);
        }
        return $res;
    }

    public function actionComentarios($cancion_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $owner = Canciones::find()
            ->where(['id' => $cancion_id])
            ->andWhere(['usuario_id' => Yii::$app->user->id])
            ->exists();

        $comentarios = (new \yii\db\Query())
            ->select(['usuarios.id', 'usuarios.login', 'comentario', 'usuarios.url_image', 'c.created_at', 'c.id as comentario_id'])
            ->from('comentarios c')
            ->leftJoin('usuarios', 'usuarios.id = c.usuario_id')
            ->where(['cancion_id' => $cancion_id])
            ->orderBy('c.id DESC')
            ->all();

        foreach ($comentarios as &$comentario) {
            $comentario['login'] = Html::encode($comentario['login']);
            $comentario['created_at'] = Yii::$app->formatter->asRelativeTime($comentario['created_at']);
            $comentario['comentario'] = Html::encode($comentario['comentario']);
        }

        return [
            'comentarios' => $comentarios,
            'owner' => $owner,
            'loggedUserId' => Yii::$app->user->id,
        ];
    }

    public function actionGetLikes($cancion_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $canciones = (new \yii\db\Query())
            ->select(['usuarios.login', 'usuarios.url_image', 'usuarios.id'])
            ->from('likes l')
            ->leftJoin('usuarios', 'l.usuario_id = usuarios.id')
            ->where(['cancion_id' => $cancion_id])
            ->all();

        Yii::debug($canciones);

        return $canciones;
    }
}
