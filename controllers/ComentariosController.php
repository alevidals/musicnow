<?php

namespace app\controllers;

use app\models\Canciones;
use Yii;
use app\models\Comentarios;
use app\models\ComentariosSearch;
use app\models\Usuarios;
use DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Response;

/**
 * ComentariosController implements the CRUD actions for Comentarios model.
 */
class ComentariosController extends Controller
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
            // 'access' => [
            //     'class' => AccessControl::className(),
            //     'only' => ['index', 'update', 'create', 'delete'],
            //     'rules' => [
            //         [
            //             'allow' => true,
            //             'roles' => ['@'],
            //             'matchCallback' => function ($rules, $action) {
            //                 $user_id = Yii::$app->request->get('user_id');
            //                 $id = Yii::$app->request->get('id');
            //                 $comentarios = Usuarios::findOne(Yii::$app->user->id)->getComentarios()->select('id')->column();
            //                 return (Yii::$app->user->identity->login === 'admin'
            //                     && Yii::$app->user->identity->rol === 1)
            //                     || ($user_id == Yii::$app->user->id)
            //                     || (in_array($id, $comentarios));
            //             },
            //         ],
            //     ],
            // ],
        ];
    }

    /**
     * Lists all Comentarios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ComentariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Comentarios model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Comentarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Comentarios();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Comentarios model.
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
     * Deletes an existing Comentarios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return Yii::t('app', 'Are you sure you want to delete this item?');
    }

    /**
     * Finds the Comentarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Comentarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Comentarios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionComentar($cancion_id)
    {
        $usuario = Usuarios::findOne(['id' =>Yii::$app->user->id ]);
        $model = new Comentarios();
        $comentario = Yii::$app->request->post('comentario');

        $model->usuario_id = $usuario->id;
        $model->cancion_id = $cancion_id;
        $model->comentario = $comentario;

        $model->save();
        $model->refresh();

        $owner = Canciones::find()
            ->where(['id' => $cancion_id])
            ->andWhere(['usuario_id' => Yii::$app->user->id])
            ->exists();

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'login' => Html::encode($usuario->login),
            'comentario' => Html::encode($model->comentario),
            'usuario_id' => $usuario->id,
            'url_image' => $usuario->url_image,
            'created_at' => Yii::$app->formatter->asRelativeTime($model->created_at),
            'owner' => $owner,
            'comentario_id' => $model->id,
        ];
    }
}
