<?php

namespace modules\flight\controllers;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use sales\helpers\app\AppHelper;
use Yii;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\models\search\FlightQuotePaxPriceSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FlightQuotePaxPriceController implements the CRUD actions for FlightQuotePaxPrice model.
 */
class FlightQuotePaxPriceController extends FController
{

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all FlightQuotePaxPrice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FlightQuotePaxPriceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FlightQuotePaxPrice model.
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
     * Creates a new FlightQuotePaxPrice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FlightQuotePaxPrice();

        if ($model->load(Yii::$app->request->post())) {

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new \RuntimeException('FlightQuotePaxPrice not saved');
                }
                $productQuote = $this->getProductQuote($model);
                $productQuote->profitAmount();
                $productQuoteRepository = Yii::$container->get(ProductQuoteRepository::class);
                $productQuoteRepository->save($productQuote);
                $transaction->commit();
            } catch (\Throwable $throwable) {
                $transaction->rollBack();
                Yii::error(AppHelper::throwableFormatter($throwable), self::class . ':' . __FUNCTION__ );
            }

            return $this->redirect(['view', 'id' => $model->qpp_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing FlightQuotePaxPrice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $checkProfit = $model->isAttributeChanged('qpp_system_mark_up') || $model->isAttributeChanged('qpp_agent_mark_up');
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new \RuntimeException('FlightQuotePaxPrice not saved');
                }
                if ($checkProfit) {
                    $productQuote = $this->getProductQuote($model);
                    $productQuote->profitAmount();
                    $productQuoteRepository = Yii::$container->get(ProductQuoteRepository::class);
                    $productQuoteRepository->save($productQuote);
                }
                $transaction->commit();
            } catch (\Throwable $throwable) {
                $transaction->rollBack();
                Yii::error(AppHelper::throwableFormatter($throwable), self::class . ':' . __FUNCTION__ );
            }
            return $this->redirect(['view', 'id' => $model->qpp_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing FlightQuotePaxPrice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = $this->findModel($id);
            $productQuote = $this->getProductQuote($model);
            $model->delete();
            $productQuote->profitAmount();
            $productQuoteRepository = Yii::$container->get(ProductQuoteRepository::class);
            $productQuoteRepository->save($productQuote);

            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::error(AppHelper::throwableFormatter($throwable), self::class . ':' . __FUNCTION__ );
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the FlightQuotePaxPrice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FlightQuotePaxPrice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FlightQuotePaxPrice::findOne($id)) !== null) {y
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param FlightQuotePaxPrice $model
     * @return ProductQuote
     */
    private function getProductQuote(FlightQuotePaxPrice $model): ProductQuote
    {
        return $model->qppFlightQuote->fqProductQuote;
    }
}
