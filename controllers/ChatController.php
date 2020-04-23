<?php

namespace app\controllers;

use Yii;
use app\models\Chat;
use app\models\ChatSearch;
use app\models\Usuarios;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Response;

/**
 * ChatController implements the CRUD actions for Chat model.
 */
class ChatController extends Controller
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
     * Lists all Chat models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ChatSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Chat model.
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
     * Creates a new Chat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Chat();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Chat model.
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
     * Deletes an existing Chat model.
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
     * Finds the Chat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Chat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Chat::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionChat()
    {

        $usuario = Usuarios::findOne(Yii::$app->user->id);
        $seguidos = $usuario->getSeguidos()->all();

        return $this->render('chat', [
            'usuario' => $usuario,
            'seguidos' => $seguidos
        ]);
    }

    public function actionSendChat()
    {
        $post = Yii::$app->request->post();
        $usuario = Usuarios::findOne(Yii::$app->user->id);
        $receptor_id = $post['receptor_id'];

        $chat = new Chat();
        $chat->emisor_id = $usuario->id;
        $chat->receptor_id = $receptor_id;
        $chat->mensaje = $post['mensaje'];
        $chat->save();

        $historial = $this->actionGetChat($receptor_id)['historial'];

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $historial;
    }

    public function actionGetChat($receptor_id)
    {
        $usuario = Usuarios::findOne(Yii::$app->user->id);

        $historial = (new \yii\db\Query())
            ->select(['c.emisor_id', 'c.receptor_id', 'mensaje', 'usuarios.login', 'c.created_at', 'c.estado_id'])
            ->from('chat c')
            ->leftJoin('usuarios', 'usuarios.id = c.emisor_id')
            ->where(['emisor_id' => $usuario->id])
            ->andWhere(['receptor_id' => $receptor_id])
            ->orWhere(['and',
                ['emisor_id' => $receptor_id],
                ['receptor_id' => $usuario->id],
            ])
            ->orderBy(['c.created_at' => SORT_ASC])
            ->all();


        foreach ($historial as &$mensaje) {
            $mensaje['created_at'] = Yii::$app->formatter->asTime($mensaje['created_at']);
            $mensaje['mensaje'] = Html::encode($mensaje['mensaje']);
        }

        Chat::updateAll(['estado_id' => 4], ['emisor_id' => $receptor_id, 'receptor_id' => $usuario->id, 'estado_id' => 3]);

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'historial' => $historial,
            'emisor' => $usuario->login,
            'receptor' => Usuarios::findOne($receptor_id)->login,
        ];
    }
}
