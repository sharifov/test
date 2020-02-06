<?php

namespace modules\product\controllers;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\services\productQuote\ProductQuoteCloneService;
use sales\auth\Auth;
use Yii;
use frontend\controllers\FController;
use modules\hotel\models\HotelQuote;
use modules\product\src\entities\productType\ProductType;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ProductQuoteController
 *
 * @property ProductQuoteCloneService $productQuoteCloneService
 */
class ProductQuoteController extends FController
{
    private $productQuoteCloneService;

    public function __construct($id, $module, ProductQuoteCloneService $productQuoteCloneService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->productQuoteCloneService = $productQuoteCloneService;
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
                    'delete-ajax' => ['POST'],
                    'clone' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionClone(): Response
    {
        $productQuoteId = (int)Yii::$app->request->post('id');
        $productQuote = $this->findModel($productQuoteId);

        if (!$productQuote->pqProduct) {
            return $this->asJson(['error' => 'Error: not found relation Product']);
        }

        try {
            $clone = $this->productQuoteCloneService->clone($productQuote->pq_id, $productQuote->pqProduct->pr_id, Auth::id(), Auth::id());
            return $this->asJson(['message' => 'Successfully cloned product quote. New product quote (' . $clone->pq_id . ')']);
        } catch (\DomainException $e) {
            return $this->asJson(['error' => 'Error: ' . $e->getMessage()]);
        } catch (\Throwable $e) {
            Yii::error($e, 'ProductQuoteController:actionClone');
            return $this->asJson(['error' => 'Server error']);
        }
    }

    /**
     * @return array
     */
    public function actionDeleteAjax(): array
    {
        $id = (int)Yii::$app->request->post('id');

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {

            if (!$id) {
                throw new Exception('Product quote ID not found', 3);
            }

            $model = $this->findModel($id);
            if (!$model->delete()) {
                throw new Exception('Product Quote (' . $id . ') not deleted', 4);
            }

            if ((int)$model->pqProduct->pr_type_id === ProductType::PRODUCT_HOTEL && class_exists('\modules\hotel\HotelModule')) {
                $modelHotelQuote = HotelQuote::findOne(['hq_product_quote_id' => $model->pq_id]);
                if ($modelHotelQuote) {
                    if (!$modelHotelQuote->delete()) {
                        throw new Exception('Hotel Quote (' . $modelHotelQuote->hq_id . ') not deleted', 5);
                    }
                }
            }

        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed product quote (' . $model->pq_id . ')'];
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
