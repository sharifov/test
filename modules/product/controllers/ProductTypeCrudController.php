<?php

namespace modules\product\controllers;

use frontend\controllers\FController;
use modules\product\src\entities\product\Product;
use sales\auth\Auth;
use sales\dispatchers\EventDispatcher;
use sales\helpers\app\AppHelper;
use Yii;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\entities\productType\search\ProductTypeCrudSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ProductTypeController implements the CRUD actions for ProductType model.
 * @property EventDispatcher $eventDispatcher
 */
class ProductTypeCrudController extends FController
{
    private $eventDispatcher;

    /**
     * ProductTypeCrudController constructor.
     * @param $id
     * @param $module
     * @param EventDispatcher $eventDispatcher
     * @param array $config
     */
    public function __construct($id, $module, EventDispatcher $eventDispatcher, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->eventDispatcher = $eventDispatcher;
    }
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ProductTypeCrudSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ProductType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->pt_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $afterFeeAmount = $model->getProcessingFeeAmount();

        if ($model->load(Yii::$app->request->post())) {

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new \RuntimeException('ProductType not saved');
                }
                $beforeFeeAmount = $model->getProcessingFeeAmount();
                if ($afterFeeAmount !== $beforeFeeAmount) {
                    foreach ($model->products as $product) {
                        foreach ($product->productQuotes as $productQuote) {
                            $productQuote->profitAmount();
                            $this->eventDispatcher->dispatchAll($productQuote->releaseEvents());
                        }
                    }
                }
                $transaction->commit();
            } catch (\Throwable $throwable) {
                $transaction->rollBack();
                Yii::error(AppHelper::throwableFormatter($throwable), self::class . ':' . __FUNCTION__ );
            }
            return $this->redirect(['view', 'id' => $model->pt_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return ProductType
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ProductType
    {
        if (($model = ProductType::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
