<?php

namespace sales\model\quoteLabel\service;

use sales\helpers\ErrorsToStringHelper;
use sales\model\quoteLabel\entity\QuoteLabel;
use sales\model\quoteLabel\repository\QuoteLabelRepository;
use yii\helpers\ArrayHelper;

/**
 * Class QuoteLabelService
 */
class QuoteLabelService
{
    public static function processingQuoteLabel(array $quoteData, int $quoteId, string $valueKey = 'meta.prod_types'): void
    {
        if ($prodTypes = ArrayHelper::getValue($quoteData, $valueKey)) {
            if (is_array($prodTypes)) {
                foreach ($prodTypes as $label) {
                    self::createQuoteLabel($quoteId, $label);
                }
            } elseif (is_string($prodTypes)) {
                self::createQuoteLabel($quoteId, $prodTypes);
            } else {
                \Yii::warning(['error' => 'Unknown data type', 'data' => $prodTypes], 'QuoteLabelService:createQuoteLabel:prodTypes');
            }
        }
    }

    public static function createQuoteLabel(int $quoteId, string $label): ?QuoteLabel
    {
        $quoteLabel = QuoteLabel::create($quoteId, $label);
        if (!$quoteLabel->validate()) {
            \Yii::warning(ErrorsToStringHelper::extractFromModel($quoteLabel), 'QuoteLabelService:createQuoteLabel:save');
            return null;
        }
        (new QuoteLabelRepository())->save($quoteLabel);
        return $quoteLabel;
    }
}
