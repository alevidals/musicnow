<?php

namespace app\controllers;

use app\models\Albumes;
use Yii;
use app\models\CancionesPlaylist;
use app\models\CancionesPlaylistSearch;
use app\models\Playlists;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Response;

/**
 * CancionesPlaylistController implements the CRUD actions for CancionesPlaylist model.
 */
class CancionesPlaylistController extends Controller
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
                'only' => ['index', 'update', 'create'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rules, $action) {
                            return Yii::$app->user->identity->rol_id === 1;
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all CancionesPlaylist models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CancionesPlaylistSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CancionesPlaylist model.
     * @param integer $playlist_id
     * @param integer $cancion_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($playlist_id, $cancion_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($playlist_id, $cancion_id),
        ]);
    }

    /**
     * Creates a new CancionesPlaylist model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CancionesPlaylist();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'playlist_id' => $model->playlist_id, 'cancion_id' => $model->cancion_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CancionesPlaylist model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $playlist_id
     * @param integer $cancion_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($playlist_id, $cancion_id)
    {
        $model = $this->findModel($playlist_id, $cancion_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'playlist_id' => $model->playlist_id, 'cancion_id' => $model->cancion_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CancionesPlaylist model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $playlist_id
     * @param integer $cancion_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($playlist_id, $cancion_id)
    {
        $this->findModel($playlist_id, $cancion_id)->delete();
    }

    /**
     * Finds the CancionesPlaylist model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $playlist_id
     * @param integer $cancion_id
     * @return CancionesPlaylist the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($playlist_id, $cancion_id)
    {
        if (($model = CancionesPlaylist::findOne(['playlist_id' => $playlist_id, 'cancion_id' => $cancion_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionAgregar()
    {
        $cancion_id = Yii::$app->request->post('cancion_id');
        $playlist_id = Yii::$app->request->post('playlist_id');
        $cancion_playlist = new CancionesPlaylist();
        $cancion_playlist->cancion_id = $cancion_id;
        $cancion_playlist->playlist_id = $playlist_id;
        $cancion_playlist->save();
    }
}
