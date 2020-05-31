<?php

namespace app\controllers;

use app\models\Pagos;
use app\models\PagosSearch;
use app\models\Provincias;
use app\models\Usuarios;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
            'provincias' => ['' => ''] + Provincias::lista(),
        ]);
    }

    /**
     * Updates an existing Pagos model.
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
     * Deletes an existing Pagos model.
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
     * Finds the Pagos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
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

    /**
     * Acción que se encarga de realizar el checkout del pago
     *
     * @return void
     */
    public function actionCheckout()
    {
        $params = [
            'method' => 'paypal',
            'intent' => 'sale',
            'order' => [
                'description' => 'Premium Account',
                'subtotal' => 10,
                'shippingCost' => 0,
                'total' => 10,
                'currency' => 'EUR',
                'items' => [
                    [
                        'name' => 'Premium Account',
                        'price' => 10,
                        'quantity' => 1,
                        'currency' => 'EUR',
                    ],
                ],
            ],
        ];

        Yii::$app->PayPalRestApi->checkOut($params);
    }

    /**
     * Acción que se encarga de realizar el pago
     *
     * @return Response
     */
    public function actionMakePayment()
    {
        // Setup order information array
        $params = [
            'order' => [
                'description' => 'Premium Account',
                'subtotal' => 10,
                'shippingCost' => 0,
                'total' => 10,
                'currency' => 'EUR',
            ],
        ];

        Yii::$app->PayPalRestApi->processPayment($params);
        $data = Yii::$app->response->data;

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

        return $this->redirect(['pagos/payed']);
    }

    /**
     * Acción que se encarga de renderizar la vista de pago
     *
     * @return string
     */
    public function actionPayed()
    {
        return $this->render('payed');
    }

    /**
     * Acción que se encarga de generar la factura del pago realizado
     *
     * @return mixed
     */
    public function actionGetInvoice()
    {
        $pago = Pagos::find()
            ->where(['usuario_id' => Yii::$app->user->id])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();

        $pdf = Yii::$app->pdf;
        $pdf->content = $this->renderPartial('_invoice', [
            'pago' => $pago,
        ]);
        return $pdf->render();
    }
}
