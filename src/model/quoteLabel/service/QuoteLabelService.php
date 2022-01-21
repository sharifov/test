<?php

namespace src\model\quoteLabel\service;

use common\models\Quote;
use src\helpers\ErrorsToStringHelper;
use src\model\quoteLabel\entity\QuoteLabel;
use src\model\quoteLabel\repository\QuoteLabelRepository;
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
            throw new \RuntimeException('QuoteLabel not saved. Quote ID(' . $quoteId . ') ' . ' Label(' . $label . '). ' .
                ErrorsToStringHelper::extractFromModel($quoteLabel));
        }
        (new QuoteLabelRepository())->save($quoteLabel);
        return $quoteLabel;
    }

    public static function cloneByQuote(Quote $oldQuote, int $newQuoteId): array
    {
        $result = [];
        if ($oldQuote->quoteLabel) {
            foreach ($oldQuote->quoteLabel as $quoteLabel) {
                $result[] = self::createQuoteLabel($newQuoteId, $quoteLabel->ql_label_key);
            }
        }
        return $result;
    }
}
