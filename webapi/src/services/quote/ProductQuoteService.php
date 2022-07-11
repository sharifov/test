<?php

namespace webapi\src\services\quote;

use modules\product\src\entities\productQuote\ProductQuote;
use webapi\src\request\RequestBoInterface;
use yii\base\Model;

class ProductQuoteService implements RequestBoInterface
{
    /**
     * @param Model $model
     * @return array
     */
    public function prepareAdditionalInfo(Model $model): array
    {
        $additionalInfo = [
            'user' => [
                'name' => null,
                'email' => null,
            ],
            'quote' => [
                'created' => null,
                'expire' => null,
            ],
        ];

        if (!$model instanceof ProductQuote) {
            return $additionalInfo;
        }

        $createdBy = $model->getPqCreatedUser()->limit(1)->one();
        if ($createdBy) {
            $additionalInfo['user']['name'] = $createdBy->full_name ?? null;
            $additionalInfo['user']['email'] = $createdBy->email ?? null;
        }
        $additionalInfo['quote']['created'] = $model->pq_created_dt ?? null;
        $additionalInfo['quote']['expire'] = $model->pq_expiration_dt ?? null;

        return $additionalInfo;
    }
}
