<?php

namespace webapi\src\services\quote;

use modules\featureFlag\FFlag;
use modules\flight\src\useCases\voluntaryExchangeCreate\service\VoluntaryExchangeCreateService;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use webapi\src\request\RequestBoInterface;
use Yii;
use yii\base\Model;

class ProductQuoteService implements RequestBoInterface
{
    /**
     * @param Model $model
     * @return array
     */
    public function prepareAdditionalInfo(Model $model): array
    {
        /** @fflag FFlag::FF_KEY_SEND_ADDITIONAL_INFO_TO_BO_ENDPOINTS, Send additional info to BO endpoints enable\disable */
        if (!Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_SEND_ADDITIONAL_INFO_TO_BO_ENDPOINTS)) {
            return [];
        }

        $additionalInfo = [
            'user' => [
                'name' => null,
                'email' => null,
            ],
            'quote' => [
                'created' => null,
                'expire' => null,
            ],
            'quote_gid' => null,
        ];

        if (!$model instanceof ProductQuote) {
            return $additionalInfo;
        }

        $productQuoteChange = VoluntaryExchangeCreateService::getLastProductQuoteChangeByPqId(
            $model->pq_id,
            [ProductQuoteChangeStatus::IN_PROGRESS]
        );

        if ($productQuoteChange) {
            $voluntaryQuote = VoluntaryExchangeCreateService::getProductQuoteByProductQuoteChange(
                $productQuoteChange->pqc_id,
                [ProductQuoteStatus::IN_PROGRESS]
            );
            if ($voluntaryQuote) {
                $model = $voluntaryQuote;
            }
        }

        $createdBy = $model->getPqCreatedUser()->limit(1)->one();
        if ($createdBy) {
            $additionalInfo['user']['name'] = $createdBy->full_name ?? null;
            $additionalInfo['user']['email'] = $createdBy->email ?? null;
        }
        $additionalInfo['quote']['created'] = $model->pq_created_dt ?? null;
        $additionalInfo['quote']['expire'] = $model->pq_expiration_dt ?? null;
        $additionalInfo['quote_gid'] = $model->pq_gid ?? null;

        return $additionalInfo;
    }
}
