<?php

namespace modules\flight\src\entities\flightQuoteLabel\service;

use modules\flight\models\FlightQuote;
use modules\flight\src\entities\flightQuoteLabel\FlightQuoteLabel;
use modules\flight\src\entities\flightQuoteLabel\repository\FlightQuoteLabelRepository;
use src\helpers\ErrorsToStringHelper;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteLabelService
 */
class FlightQuoteLabelService
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
                \Yii::warning(['error' => 'Unknown data type', 'data' => $prodTypes], 'FlightQuoteLabelService:createQuoteLabel:prodTypes');
            }
        }
    }

    public static function createQuoteLabel(int $quoteId, string $label): ?FlightQuoteLabel
    {
        $quoteLabel = FlightQuoteLabel::create($quoteId, $label);
        if (!$quoteLabel->validate()) {
            \Yii::warning(ErrorsToStringHelper::extractFromModel($quoteLabel), 'FlightQuoteLabelService:createQuoteLabel:save');
            return null;
        }
        (new FlightQuoteLabelRepository())->save($quoteLabel);
        return $quoteLabel;
    }

    public static function cloneByQuote(FlightQuote $oldQuote, int $newQuoteId): array
    {
        $result = [];
        if ($oldQuote->quoteLabel) {
            foreach ($oldQuote->quoteLabel as $quoteLabel) {
                $result[] = self::createQuoteLabel($newQuoteId, $quoteLabel->fql_label_key);
            }
        }
        return $result;
    }
}
