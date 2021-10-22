<?php

namespace modules\product\controllers;

use modules\product\src\abac\ProductQuoteAbacObject;
use modules\product\src\entities\productQuoteObjectRefund\search\ProductQuoteObjectRefundSearch;
use modules\product\src\entities\productQuoteOptionRefund\search\ProductQuoteOptionRefundSearch;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

/**
 * Class FlightQuoteRefundController
 * @package modules\flight\controllers
 *
 * @property-read ProductQuoteRefundRepository $productQuoteRefundRepository
 */
class ProductQuoteRefundController extends \frontend\controllers\FController
{
    private ProductQuoteRefundRepository $productQuoteRefundRepository;

    public function __construct(
        $id,
        $module,
        ProductQuoteRefundRepository $productQuoteRefundRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->productQuoteRefundRepository = $productQuoteRefundRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'ajax-view-details'
                ]
            ]
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionAjaxViewDetails()
    {
        $productQuoteId = Yii::$app->request->get('id');

        if (!Yii::$app->abac->can(null, ProductQuoteAbacObject::ACT_VIEW_DETAILS_REFUND_QUOTE, ProductQuoteAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $productQuoteRefund = $this->productQuoteRefundRepository->find($productQuoteId);
        $dataProvider = new ActiveDataProvider();
        $dataProvider->setModels([$productQuoteRefund]);
        $dataProvider->pagination = false;

        $productQuoteObjectRefundSearch = new ProductQuoteObjectRefundSearch();
        $objectsRefundProvider = $productQuoteObjectRefundSearch->search([$productQuoteObjectRefundSearch->formName() => [
            'pqor_product_quote_refund_id' => $productQuoteId
        ]]);

        $productQuoteOptionsRefundSearch = new ProductQuoteOptionRefundSearch();
        $optionsRefundProvider = $productQuoteOptionsRefundSearch->search([$productQuoteOptionsRefundSearch->formName() => [
            'pqor_product_quote_refund_id' => $productQuoteId
        ]]);

        return $this->renderAjax('partial/_quote_view_details', [
            'dataProvider' => $dataProvider,
            'objectsRefundProvider' => $objectsRefundProvider,
            'optionsRefundProvider' => $optionsRefundProvider
        ]);
    }
}
