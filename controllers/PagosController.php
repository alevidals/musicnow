<?php

namespace app\controllers;

use Yii;
use app\models\Pagos;
use app\models\PagosSearch;
use app\models\Provincias;
use app\models\Usuarios;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PagosController implements the CRUD actions for Pagos model.
 */
class PagosController extends Controller
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
     * Lists all Pagos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PagosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Pagos model.
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
     * Creates a new Pagos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Pagos(['usuario_id' => Yii::$app->user->id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect('checkout');
        }

        return $this->render('create', [
            'model' => $model,
            'provincias' => ['' => ''] + Provincias::lista()
        ]);
    }

    /**
     * Updates an existing Pagos model.
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
     * Deletes an existing Pagos model.
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
     * Finds the Pagos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pagos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pagos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionCheckout()
    {
        // Setup order information array with all items
        $params = [
            'method'=>'paypal',
            'intent'=>'sale',
            'order'=>[
                'description'=>'Premium Account',
                'subtotal'=>10,
                'shippingCost'=>0,
                'total'=>10,
                'currency'=>'EUR',
                'items'=>[
                    [
                        'name'=>'Premium Account',
                        'price'=>10,
                        'quantity'=>1,
                        'currency'=>'EUR'
                    ],
                ]

            ]
        ];

        // In this action you will redirect to the PayPpal website to login with you buyer account and complete the payment
        Yii::$app->PayPalRestApi->checkOut($params);
    }

    public function actionMakePayment()
    {
        // Setup order information array
        $params = [
            'order'=>[
                'description'=>'Premium Account',
                'subtotal'=>10,
                'shippingCost'=>0,
                'total'=>10,
                'currency'=>'EUR',
            ]
        ];

        Yii::$app->PayPalRestApi->processPayment($params);
        $data = Yii::$app->response->data;
        var_dump($data);

        if ($data !== null && (Yii::$app->request->get('success') == true)) {
            $usuario = Usuarios::findOne(Yii::$app->user->id);
            $usuario->rol_id = 3;
            $usuario->save();
            $pago = $usuario->getPagos()->orderBy(['id' => SORT_DESC])->limit(1)->one();
            $pago->payment = $data->id;
            $pago->cart = $data->cart;
            $pago->save();
        } else {
            $pago = $usuario->getPagos()->orderBy(['id' => SORT_DESC])->limit(1)->one();
            $pago->delete();
        }

        return $this->redirect(['site/index']);
    }
}
