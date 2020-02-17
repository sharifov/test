<?php

namespace modules\product\controllers;

use modules\offer\src\entities\offer\events\OfferRecalculateProfitAmountEvent;
use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use sales\auth\Auth;
use sales\dispatchers\EventDispatcher;
use sales\helpers\app\AppHelper;
use Yii;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\search\ProductQuoteCrudSearch;
use frontend\controllers\FController;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ProductQuoteController implements the CRUD actions for ProductQuote model.
 *
 * @property EventDispatcher $eventDispatcher
 * @property ProductQuoteRepository $productQuoteRepository
 */
class ProductQuoteCrudController extends FController
{
    /**
	 * @var EventDispatcher
	 */
    private $eventDispatcher;
    /**
	 * @var ProductQuoteRepository
	 */
	private $productQuoteRepository;

    /**
     * ProductQuoteCrudController constructor.
     * @param $id
     * @param $module
     * @param EventDispatcher $eventDispatcher
     * @param ProductQuoteRepository $productQuoteRepository
     * @param array $config
     */
    public function __construct($id, $module, EventDispatcher $eventDispatcher, ProductQuoteRepository $productQuoteRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->eventDispatcher = $eventDispatcher;
        $this->productQuoteRepository = $productQuoteRepository;
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
        $searchModel = new ProductQuoteCrudSearch();
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
        $model = new ProductQuote();

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->profitAmount();
                $this->productQuoteRepository->save($model);
                $transaction->commit();
            } catch (\Throwable $throwable) {
                $transaction->rollBack();
                Yii::error(AppHelper::throwableFormatter($throwable),  'ProductQuoteCrudController:' . __FUNCTION__ );
            }
            return $this->redirect(['view', 'id' => $model->pq_id]);
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

        if ($model->load(Yii::$app->request->post())) {

            if ($model->isAttributeChanged('pq_status_id') || $model->isAttributeChanged('pq_profit_amount')) {
                $model->recalculateOffersOrders();
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $this->productQuoteRepository->save($model);
                $transaction->commit();
            } catch (\Throwable $throwable) {
                $transaction->rollBack();
                Yii::error(AppHelper::throwableFormatter($throwable),  'ProductQuoteCrudController:' . __FUNCTION__ );
            }
            return $this->redirect(['view', 'id' => $model->pq_id]);
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
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = $this->productQuoteRepository->find($id);
            $model->prepareRemove();
            $this->productQuoteRepository->remove($model);
            $transaction->commit();

        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::error(AppHelper::throwableFormatter($throwable),  'ProductQuoteCrudController:' . __FUNCTION__ );
        }

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return ProductQuote
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ProductQuote
    {
        if (($model = ProductQuote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
