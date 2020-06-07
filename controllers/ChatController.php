<?php

namespace app\controllers;

use app\models\Chat;
use app\models\ChatSearch;
use app\models\Usuarios;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
                            return Yii::$app->user->identity->rol_id === 1;
                        },
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
     * @param int $id
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
     * Deletes an existing Chat model.
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
     * Finds the Chat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
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

    /**
     * Devuelve el resultado de una ActiveQuery en la que se comprueba
     * que usuarios se siguen mutuamente.
     *
     * @return ActiveQuery
     */
    public function actionChat()
    {
        $seguidos = Usuarios::findMutualFollow()->all();

        return $this->render('chat', [
            'seguidos' => $seguidos,
        ]);
    }

    /**
     * Guarda en la base de datos el mensaje enviado por el usuario.
     *
     * @return array el chat actualizado con el nuevo mensaje enviado
     */
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

        $historial = $this->actionGetChat($receptor_id, false)['historial'];

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $historial;
    }

    /**
     * Devuelve el chat entre el usuario autenticado y el especificado
     * por parámetros.
     *
     * @param int $receptor_id usuario del que queremos obtener el chat
     * @param bool $refresh si se refresca el estado del mensaje o no
     * @return array el historial de mensajes, el nombre del emisor y el
     * nombre del receptor
     */
    public function actionGetChat($receptor_id, $refresh)
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

        if ($refresh) {
            Chat::updateAll(['estado_id' => 4], ['emisor_id' => $receptor_id, 'receptor_id' => $usuario->id, 'estado_id' => 3]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'historial' => $historial,
            'emisor' => $usuario->login,
            'receptor' => Usuarios::findOne($receptor_id)->login,
        ];
    }

    /**
     * Devuelve un array con los usuarios que se siguen mutuamente.
     *
     * @param string $text el nombre del login por el que vamos a filtrar
     * al buscar los usuarios
     * @return ActiveQuery
     */
    public function actionGetUsers($text)
    {
        $usuarios = Usuarios::findMutualFollow()
            ->andWhere(['ilike', 'login', $text])
            ->all();

        foreach ($usuarios as &$usuario) {
            $usuario->estado_id = $usuario->getEstado()->one()->estado;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $usuarios;
    }

    public function actionViewMessage($id)
    {
        $mensaje = $this->findModel($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $mensaje->notified = true;
        $mensaje->save();
    }
}
