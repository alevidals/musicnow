<?php

namespace app\controllers;

use Yii;
use app\models\SolicitudesSeguimiento;
use app\models\SolicitudesSeguimientoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * SolicitudesSeguimientoController implements the CRUD actions for SolicitudesSeguimiento model.
 */
class SolicitudesSeguimientoController extends Controller
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
     * Lists all SolicitudesSeguimiento models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SolicitudesSeguimientoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SolicitudesSeguimiento model.
     * @param integer $seguidor_id
     * @param integer $seguido_id
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
     * Creates a new SolicitudesSeguimiento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SolicitudesSeguimiento();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'seguidor_id' => $model->seguidor_id, 'seguido_id' => $model->seguido_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SolicitudesSeguimiento model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $seguidor_id
     * @param integer $seguido_id
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
     * Deletes an existing SolicitudesSeguimiento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $seguidor_id
     * @param integer $seguido_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($seguidor_id, $seguido_id)
    {
        $this->findModel($seguidor_id, $seguido_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SolicitudesSeguimiento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $seguidor_id
     * @param integer $seguido_id
     * @return SolicitudesSeguimiento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($seguidor_id, $seguido_id)
    {
        if (($model = SolicitudesSeguimiento::findOne(['seguidor_id' => $seguidor_id, 'seguido_id' => $seguido_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Acción que devuelve el total de solicitudes de seguimiento del
     * usuario autenticado
     *
     * @return array
     */
    public function actionGetTotalSolicitudes()
    {
        $total = SolicitudesSeguimiento::find()
            ->where(['seguido_id' => Yii::$app->user->id])
            ->count();

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'total' => $total,
            'message' => Yii::t('app', 'NewNotification'),
        ];
    }
}
