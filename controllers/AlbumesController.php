<?php

namespace app\controllers;

use Yii;
use app\models\Albumes;
use app\models\AlbumesSearch;
use DateTime;
use yii\bootstrap4\Html;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * AlbumesController implements the CRUD actions for Albumes model.
 */
class AlbumesController extends Controller
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
                        'actions' => ['update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rules, $action) {
                            $id = Yii::$app->request->get('id');
                            return Yii::$app->user->identity->rol_id === 1 || in_array($id, Yii::$app->user->identity->getAlbumes()->select('id')->column());
                        },
                    ],
                    [
                        'actions' => ['index', 'create'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Albumes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AlbumesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Albumes model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $model = $this->findModel($id);
        $canciones = $model->getCanciones();
        $duration = $canciones->sum('duracion');

        return $this->render('view', [
            'model' => $model,
            'canciones' => $canciones->all(),
            'duration' => $duration
        ]);
    }

    /**
     * Creates a new Albumes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Albumes(['usuario_id' => Yii::$app->user->id]);

        if (Yii::$app->request->isPost) {
            $model->portada = UploadedFile::getInstance($model, 'portada');
            $model->uploadPortada();
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                $model->deletePortada();
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Albumes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
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
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Albumes model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $canciones = $model->getCanciones()->all();

        foreach ($canciones as $cancion) {
            $cancion->deleteCancion();
            $cancion->delete();
        }

        if ($model->delete()) {
            $model->deletePortada();
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Albumes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Albumes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Albumes::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Devuelve las canciones del álbum en formato JSON
     *
     * @param int $album_id el id del álbum del que obtener las canciones
     * @return array las canciones del álbum
     */
    public function actionGetSongs($album_id)
    {
        $album = new Albumes(['id' => $album_id]);
        $canciones = $album->getCanciones()->all();
        foreach ($canciones as &$cancion) {
            $cancion->titulo = Html::encode($cancion->titulo);
            $cancion->album_id = Html::encode(Albumes::findOne($cancion->album_id)->titulo);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $canciones;
    }
}
