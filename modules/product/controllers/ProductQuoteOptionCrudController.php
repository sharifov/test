<?php

namespace modules\product\controllers;

use frontend\controllers\FController;
use sales\auth\Auth;
use sales\dispatchers\EventDispatcher;
use sales\helpers\app\AppHelper;
use Yii;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOption\search\ProductQuoteOptionCrudSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ProductQuoteOptionController implements the CRUD actions for ProductQuoteOption model.
 * @property EventDispatcher $eventDispatcher
 */
class ProductQuoteOptionCrudController extends FController
{
    private $eventDispatcher;

    /**
     * ProductQuoteOptionCrudController constructor.
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
        $searchModel = new ProductQuoteOptionCrudSearch();
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
        $model = new ProductQuoteOption();

        if ($model->load(Yii::$app->request->post())) {

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new \RuntimeException('ProductQuoteOption not saved');
                }
                $productQuote = $model->pqoProductQuote;
                $productQuote->recalculateProfitAmount();
                $this->eventDispatcher->dispatchAll($productQuote->releaseEvents());

                $transaction->commit();
            } catch (\Throwable $throwable) {
                $transaction->rollBack();
                Yii::error(AppHelper::throwableFormatter($throwable), self::class . ':' . __FUNCTION__ );
            }
            return $this->redirect(['view', 'id' => $model->pqo_id]);
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

            $checkProfit = ($model->isAttributeChanged('pqo_extra_markup') || $model->isAttributeChanged('pqo_status_id'));

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new \RuntimeException('ProductQuoteOption not saved');
                }
                if ($checkProfit) {
                    $productQuote = $model->pqoProductQuote;
                    $productQuote->recalculateProfitAmount();
                    $this->eventDispatcher->dispatchAll($productQuote->releaseEvents());
                }
                $transaction->commit();
            } catch (\Throwable $throwable) {
                $transaction->rollBack();
                Yii::error(AppHelper::throwableFormatter($throwable), self::class . ':' . __FUNCTION__ );
            }
            return $this->redirect(['view', 'id' => $model->pqo_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = $this->findModel($id);
            $productQuote = $model->pqoProductQuote;
            $model->delete();
            $productQuote->recalculateProfitAmount();
            $this->eventDispatcher->dispatchAll($productQuote->releaseEvents());

            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::error(AppHelper::throwableFormatter($throwable), self::class . ':' . __FUNCTION__ );
        }

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return ProductQuoteOption
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ProductQuoteOption
    {
        if (($model = ProductQuoteOption::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
