<?php

namespace app\controllers;

use Yii;
use app\models\Albumes;
use app\models\AlbumesSearch;
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

        return $this->render('view', [
            'model' => $model,
            'canciones' => $model->getCanciones()->all(),
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
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

        $model->delete();
        $model->deletePortada();

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
