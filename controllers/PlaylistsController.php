<?php

namespace app\controllers;

use app\models\Albumes;
use app\models\CancionesPlaylist;
use app\models\Playlists;
use app\models\PlaylistsSearch;
use app\models\Usuarios;
use Yii;
use yii\bootstrap4\Html;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
                            return Yii::$app->user->identity->rol_id === 1 || in_array($id, Yii::$app->user->identity->getPlaylists()->select('id')->column());
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
     * Lists all Playlists models.
     * @return mixed
     */
    public function actionIndex()
    {
        $res = [];

        if (Yii::$app->user->identity->rol_id == 1) {
            $searchModel = new PlaylistsSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $res = [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ];
        } else {
            $usuario = Usuarios::findOne(Yii::$app->user->id);
            $playlists = $usuario->getPlaylists()->all();
            // $likedSongs = $usuario->getCancionesFavoritas()->all();
            $res = [
                'playlists' => $playlists,
            ];
        }

        return $this->render('index', $res);
    }

    /**
     * Displays a single Playlists model.
     * @param int $id
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
     * Deletes an existing Playlists model.
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
     * Finds the Playlists model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
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

    /**
     * Acción que devuelve las canciones de la playlist especificada.
     *
     * @param int $playlist_id el id de la playlist de la que se desea
     * obtener las canciones
     * @return ActiveQuery
     */
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

    /**
     * Acción que se encarga de copiar la playlist de un usuario.
     *
     * @return string mensaje de éxito
     */
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
