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
                throw new \RuntimeException('QuoteLabel: Unknown data type');
            }
        }
    }

    public static function createQuoteLabel(int $quoteId, string $label): ?QuoteLabel
    {
        $quoteLabel = QuoteLabel::create($quoteId, $label);
        if (!$quoteLabel->validate()) {
            throw new \RuntimeException('QuoteLabel not saved. ' . ErrorsToStringHelper::extractFromModel($quoteLabel));
        }
        (new QuoteLabelRepository())->save($quoteLabel);
        return $quoteLabel;
    }
}
