<?php

namespace app\controllers;

use app\models\Canciones;
use Yii;
use app\models\Likes;
use app\models\LikesSearch;
use app\models\Seguidores;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * LikesController implements the CRUD actions for Likes model.
 */
class LikesController extends Controller
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
                        'matchCallback' => function ($rules, $action) {
                            return Yii::$app->user->identity->rol === 1;
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Likes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LikesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Likes model.
     * @param integer $usuario_id
     * @param integer $cancion_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($usuario_id, $cancion_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($usuario_id, $cancion_id),
        ]);
    }

    /**
     * Creates a new Likes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Likes();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'usuario_id' => $model->usuario_id, 'cancion_id' => $model->cancion_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Likes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $usuario_id
     * @param integer $cancion_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($usuario_id, $cancion_id)
    {
        $model = $this->findModel($usuario_id, $cancion_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'usuario_id' => $model->usuario_id, 'cancion_id' => $model->cancion_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Likes model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $usuario_id
     * @param integer $cancion_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($usuario_id, $cancion_id)
    {
        $this->findModel($usuario_id, $cancion_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Likes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $usuario_id
     * @param integer $cancion_id
     * @return Likes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($usuario_id, $cancion_id)
    {
        if (($model = Likes::findOne(['usuario_id' => $usuario_id, 'cancion_id' => $cancion_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionLike($cancion_id)
    {
        $res = [];

        $usuario_id = Yii::$app->user->id;

        $like = Likes::find()
            ->andWhere([
                'usuario_id' => $usuario_id,
                'cancion_id' => $cancion_id,
            ])
            ->one();

        if ($like === null) {
            $like = new Likes();
            $like->usuario_id = $usuario_id;
            $like->cancion_id = $cancion_id;
            $like->save();
            $res['class'] = 'fas';
        } elseif ($like !== null) {
            $this->findModel($usuario_id, $cancion_id)->delete();
            $res['class'] = 'far';
        }

        $cancion = Canciones::findOne($cancion_id);
        $res['likes'] = $cancion->getLikes()->count();

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $res;
    }

    public function actionGetData($cancion_id)
    {
        $res = [];

        $res['class'] = 'far';

        $usuario_id = Yii::$app->user->id;

        $like = Likes::findOne([
            'usuario_id' => $usuario_id,
            'cancion_id' => $cancion_id,
        ]);

        if ($like !== null) {
            $res['class'] = 'fas';
        }

        $cancion = Canciones::findOne($cancion_id);
        $res['likes'] = $cancion->getLikes()->count();

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $res;
    }
}
