<?php

namespace app\controllers;

use app\models\Chat;
use app\models\Seguidores;
use app\models\SeguidoresSearch;
use app\models\SolicitudesSeguimiento;
use app\models\Usuarios;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * SeguidoresController implements the CRUD actions for Seguidores model.
 */
class SeguidoresController extends Controller
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
                'only' => ['index', 'update', 'create', 'delete', 'follow'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'update', 'create', 'delete'],
                        'roles' => ['@'],
                        'matchCallback' => function ($rules, $action) {
                            return Yii::$app->user->identity->rol_id === 1;
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['follow'],
                        'roles' => ['@'],
                        'matchCallback' => function ($rules, $action) {
                            $seguido_id = Yii::$app->request->get('seguido_id');
                            return $seguido_id != Yii::$app->user->id;
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Seguidores models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SeguidoresSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Seguidores model.
     * @param int $seguidor_id
     * @param int $seguido_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($seguidor_id, $seguido_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($seguidor_id, $seguido_id),
        ]);
    }

    /**
     * Creates a new Seguidores model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Seguidores();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'seguidor_id' => $model->seguidor_id, 'seguido_id' => $model->seguido_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Seguidores model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $seguidor_id
     * @param int $seguido_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($seguidor_id, $seguido_id)
    {
        $model = $this->findModel($seguidor_id, $seguido_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'seguidor_id' => $model->seguidor_id, 'seguido_id' => $model->seguido_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Seguidores model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $seguidor_id
     * @param int $seguido_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($seguidor_id, $seguido_id)
    {
        $this->findModel($seguidor_id, $seguido_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Seguidores model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $seguidor_id
     * @param int $seguido_id
     * @return Seguidores the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($seguidor_id, $seguido_id)
    {
        if (($model = Seguidores::findOne(['seguidor_id' => $seguidor_id, 'seguido_id' => $seguido_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Acción que se encarga de hacer que un usuario siga al usuario
     * especificado en los parámetros
     *
     * @param int $seguido_id el usuario al que se desea seguir
     * @return array
     */
    public function actionFollow($seguido_id)
    {
        $res = [];
        $usuarioSeguido = Usuarios::findOne($seguido_id);

        $seguir = Seguidores::find()
            ->andWhere([
                'seguidor_id' => Yii::$app->user->id,
                'seguido_id' => $seguido_id,
            ]);

        if ($seguir->exists()) {
            $this->findModel(Yii::$app->user->id, $seguido_id)->delete();
            $res['textButton'] = Yii::t('app', 'Follow');
        } else {
            if ($usuarioSeguido->privated_account) {
                $solicitud = SolicitudesSeguimiento::find()
                    ->andWhere([
                        'seguidor_id' => Yii::$app->user->id,
                        'seguido_id' => $seguido_id,
                    ]);
                if ($solicitud->exists()) {
                    SolicitudesSeguimiento::findOne([
                        'seguidor_id' => Yii::$app->user->id,
                        'seguido_id' => $seguido_id,
                    ])->delete();
                    $res['textButton'] = Yii::t('app', 'Follow');
                } else {
                    $solicitud = new SolicitudesSeguimiento();
                    $solicitud->seguidor_id = Yii::$app->user->id;
                    $solicitud->seguido_id = $seguido_id;
                    $solicitud->save();
                    $res['textButton'] = Yii::t('app', 'Requested');
                }
            } else {
                $seguir = new Seguidores();
                $seguir->seguidor_id = Yii::$app->user->id;
                $seguir->seguido_id = $seguido_id;
                $seguir->save();
                $res['textButton'] = Yii::t('app', 'Unfollow');
            }
        }

        $res['seguidores'] = $usuarioSeguido->getSeguidores()->count();

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $res;
    }

    /**
     * Devuelve la información del seguimiento de un usuario
     *
     * @param int $seguido_id el id del usuario del que queremos comprobar
     * la información
     * @return array
     */
    public function actionGetData($seguido_id)
    {

        $res = [];

        $res['textButton'] = Yii::t('app', 'Follow');
        $user = Usuarios::findOne($seguido_id);

        $seguir = Seguidores::find()
            ->andWhere([
                'seguidor_id' => Yii::$app->user->id,
                'seguido_id' => $seguido_id,
            ]);

        if ($seguir->exists()) {
            $res['textButton'] = Yii::t('app', 'Unfollow');
        } else {
            if ($user->privated_account) {
                $solicitud = SolicitudesSeguimiento::find()
                    ->andWhere([
                        'seguidor_id' => Yii::$app->user->id,
                        'seguido_id' => $seguido_id,
                    ]);
                if ($solicitud->exists()) {
                    $res['textButton'] = Yii::t('app', 'Requested');
                }
            }
        }

        $res['seguidores'] = $user->getSeguidores()->count();
        $res['seguidos'] = $user->getSeguidos()->count();

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $res;
    }

    /**
     * Acción que se encarga de hacer que un usuario nos deje de seguir
     *
     * @param int $seguidor_id el id del usuario que queremos hacer que
     * nos deje de seguir
     * @return void
     */
    public function actionDeleteFollower($seguidor_id)
    {
        $userId = Yii::$app->user->id;
        $model = $this->findModel($seguidor_id, $userId);
        if ($model->delete()) {
            Chat::updateAll(['estado_id' => 4], ['emisor_id' => $seguidor_id, 'receptor_id' => $userId, 'estado_id' => 3]);
            Chat::updateAll(['estado_id' => 4], ['emisor_id' => $userId, 'receptor_id' => $seguidor_id, 'estado_id' => 3]);
        }
    }

    /**
     * Acción que se encarga de crear una solicitud de seguimiento si el
     * usuario al que vamos a seguir tiene la cuenta privada
     *
     * @return void
     */
    public function actionSolicitud()
    {
        $seguidor_id = Yii::$app->request->post('seguidor_id');
        $type = Yii::$app->request->post('type');

        if ($type == 'accept') {
            SolicitudesSeguimiento::findOne([
                'seguidor_id' => $seguidor_id,
                'seguido_id' => Yii::$app->user->id,
            ])->delete();

            $seguir = new Seguidores();
            $seguir->seguidor_id = $seguidor_id;
            $seguir->seguido_id = Yii::$app->user->id;
            $seguir->save();
        } elseif ($type == 'delete') {
            SolicitudesSeguimiento::findOne([
                'seguidor_id' => $seguidor_id,
                'seguido_id' => Yii::$app->user->id,
            ])->delete();
        }
    }
}
