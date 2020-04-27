<?php

namespace app\controllers;

use Yii;
use app\models\AlbumesCanciones;
use app\models\AlbumesCancionesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AlbumesCancionesController implements the CRUD actions for AlbumesCanciones model.
 */
class AlbumesCancionesController extends Controller
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
        ];
    }

    /**
     * Lists all AlbumesCanciones models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AlbumesCancionesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AlbumesCanciones model.
     * @param integer $album_id
     * @param integer $canciones_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($album_id, $canciones_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($album_id, $canciones_id),
        ]);
    }

    /**
     * Creates a new AlbumesCanciones model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AlbumesCanciones();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'album_id' => $model->album_id, 'canciones_id' => $model->canciones_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AlbumesCanciones model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $album_id
     * @param integer $canciones_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($album_id, $canciones_id)
    {
        $model = $this->findModel($album_id, $canciones_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'album_id' => $model->album_id, 'canciones_id' => $model->canciones_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AlbumesCanciones model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $album_id
     * @param integer $canciones_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($album_id, $canciones_id)
    {
        $this->findModel($album_id, $canciones_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AlbumesCanciones model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $album_id
     * @param integer $canciones_id
     * @return AlbumesCanciones the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($album_id, $canciones_id)
    {
        if (($model = AlbumesCanciones::findOne(['album_id' => $album_id, 'canciones_id' => $canciones_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
