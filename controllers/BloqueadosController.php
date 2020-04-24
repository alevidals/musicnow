<?php

namespace app\controllers;

use Yii;
use app\models\Bloqueados;
use app\models\BloqueadosSearch;
use app\models\Seguidores;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BloqueadosController implements the CRUD actions for Bloqueados model.
 */
class BloqueadosController extends Controller
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
                            return Yii::$app->user->identity->rol_id === 1;
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Bloqueados models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BloqueadosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Bloqueados model.
     * @param integer $bloqueador_id
     * @param integer $bloqueado_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($bloqueador_id, $bloqueado_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($bloqueador_id, $bloqueado_id),
        ]);
    }

    /**
     * Creates a new Bloqueados model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Bloqueados();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'bloqueador_id' => $model->bloqueador_id, 'bloqueado_id' => $model->bloqueado_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Bloqueados model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $bloqueador_id
     * @param integer $bloqueado_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($bloqueador_id, $bloqueado_id)
    {
        $model = $this->findModel($bloqueador_id, $bloqueado_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'bloqueador_id' => $model->bloqueador_id, 'bloqueado_id' => $model->bloqueado_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Bloqueados model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $bloqueador_id
     * @param integer $bloqueado_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($bloqueador_id, $bloqueado_id)
    {
        $this->findModel($bloqueador_id, $bloqueado_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Bloqueados model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $bloqueador_id
     * @param integer $bloqueado_id
     * @return Bloqueados the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($bloqueador_id, $bloqueado_id)
    {
        if (($model = Bloqueados::findOne(['bloqueador_id' => $bloqueador_id, 'bloqueado_id' => $bloqueado_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionBloquear($bloqueado_id)
    {
        $user_id = Yii::$app->user->id;

        $seguir = Seguidores::findOne(['seguidor_id' => $user_id, 'seguido_id' => $bloqueado_id]);
        if ($seguir !== null) {
            $seguir->delete();
        }

        $bloqueo = Bloqueados::find()
            ->andWhere([
                'bloqueador_id' => $user_id,
                'bloqueado_id' => $bloqueado_id
            ]);

        if (!$bloqueo->exists()) {
            $bloqueo = new Bloqueados();
            $bloqueo->bloqueador_id = $user_id;
            $bloqueo->bloqueado_id = $bloqueado_id;
            $bloqueo->save();
        } else {
            $this->findModel($user_id, $bloqueado_id)->delete();
        }

        return $this->redirect(['usuarios/perfil', 'id' => $bloqueado_id]);
    }
}
