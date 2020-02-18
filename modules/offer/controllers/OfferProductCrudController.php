<?php

namespace modules\offer\controllers;

use modules\offer\src\entities\offer\events\OfferRecalculateProfitAmountEvent;
use modules\offer\src\entities\offerProduct\OfferProductRepository;
use sales\auth\Auth;

use sales\dispatchers\EventDispatcher;
use sales\helpers\app\AppHelper;
use Yii;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\offer\src\entities\offerProduct\search\OfferProductCrudSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * @property OfferProductRepository $offerProductRepository
 * @property EventDispatcher $eventDispatcher
 */
class OfferProductCrudController extends FController
{
    private $offerProductRepository;
    private $eventDispatcher;

    /**
     * OfferProductCrudController constructor.
     * @param $id
     * @param $module
     * @param OfferProductRepository $offerProductRepository
     * @param EventDispatcher $eventDispatcher
     * @param array $config
     */
    public function __construct($id, $module, OfferProductRepository $offerProductRepository, EventDispatcher $eventDispatcher, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->offerProductRepository = $offerProductRepository;
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
        $searchModel = new OfferProductCrudSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $op_offer_id
     * @param $op_product_quote_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($op_offer_id, $op_product_quote_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($op_offer_id, $op_product_quote_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new OfferProduct();

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new \RuntimeException('FlightQuotePaxPrice not created');
                }
                $this->eventDispatcher->dispatchAll([new OfferRecalculateProfitAmountEvent([$model->opOffer])]);
                $transaction->commit();
                return $this->redirect(['view',
                    'op_offer_id' => $model->op_offer_id,
                    'op_product_quote_id' => $model->op_product_quote_id
                ]);
            } catch (\Throwable $throwable) {
                $transaction->rollBack();
                Yii::error(AppHelper::throwableFormatter($throwable),  'OfferProductCrudController:' . __FUNCTION__ );
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $op_offer_id
     * @param $op_product_quote_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($op_offer_id, $op_product_quote_id)
    {
        $model = $this->findModel($op_offer_id, $op_product_quote_id);

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new \RuntimeException('FlightQuotePaxPrice not created');
                }
                $this->eventDispatcher->dispatchAll([new OfferRecalculateProfitAmountEvent([$model->opOffer])]);
                $transaction->commit();
                return $this->redirect(['view',
                    'op_offer_id' => $model->op_offer_id,
                    'op_product_quote_id' => $model->op_product_quote_id
                ]);
            } catch (\Throwable $throwable) {
                $transaction->rollBack();
                Yii::error(AppHelper::throwableFormatter($throwable), 'OfferProductCrudController:' . __FUNCTION__ );
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $op_offer_id
     * @param $op_product_quote_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($op_offer_id, $op_product_quote_id): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = $this->offerProductRepository->find($op_offer_id, $op_product_quote_id);
            $this->eventDispatcher->dispatchAll([new OfferRecalculateProfitAmountEvent([$model->opOffer])]);
            $this->offerProductRepository->remove($model);
            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::error(AppHelper::throwableFormatter($throwable),'OfferProductCrudController:' . __FUNCTION__  . ':Exception');
        }

        return $this->redirect(['index']);
    }

    /**
     * @param $op_offer_id
     * @param $op_product_quote_id
     * @return OfferProduct
     * @throws NotFoundHttpException
     */
    protected function findModel($op_offer_id, $op_product_quote_id): OfferProduct
    {
        if (($model = OfferProduct::findOne(['op_offer_id' => $op_offer_id, 'op_product_quote_id' => $op_product_quote_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
