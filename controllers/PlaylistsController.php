<?php

namespace app\controllers;

use app\models\Albumes;
use app\models\CancionesPlaylist;
use Yii;
use app\models\Playlists;
use app\models\PlaylistsSearch;
use yii\bootstrap4\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * PlaylistsController implements the CRUD actions for Playlists model.
 */
class PlaylistsController extends Controller
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
     * Lists all Playlists models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlaylistsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Playlists model.
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
            'duration' => $duration,
        ]);
    }

    /**
     * Creates a new Playlists model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Playlists(['usuario_id' => Yii::$app->user->id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Playlists model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
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
     * Deletes an existing Playlists model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Playlists model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Playlists the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Playlists::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionGetSongs($playlist_id)
    {
        $playlist = new Playlists(['id' => $playlist_id]);
        $canciones = $playlist->getCanciones()->all();
        foreach ($canciones as &$cancion) {
            $cancion->titulo = Html::encode($cancion->titulo);
            if ($cancion->album_id != null) {
                $cancion->album_id = Html::encode(Albumes::findOne($cancion->album_id)->titulo);
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $canciones;
    }

    public function actionCopiar()
    {
        $id = Yii::$app->request->post('id');

        $copiedPlaylist = Playlists::findOne($id);
        $playlist = new Playlists(['usuario_id' => Yii::$app->user->id]);
        $playlist->titulo = $copiedPlaylist->titulo;

        if ($playlist->save()) {
            $copiedCancionesPlaylist = CancionesPlaylist::find()
                ->where(['playlist_id' => $copiedPlaylist->id])
                ->all();

            foreach ($copiedCancionesPlaylist as $copy) {
                $cancionesPlaylist = new CancionesPlaylist(['playlist_id' => $playlist->id]);
                $cancionesPlaylist->cancion_id = $copy->cancion_id;
                $cancionesPlaylist->save();
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return Yii::t('app', 'CopiedPlaylist');
        }


    }
}
