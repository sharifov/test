<?php

namespace sales\helpers\quote;

/**
 * Class AddQuoteHelper
 */
class BaggageHelper
{
    /**
     * @return array
     */
    public static function getBaggageHeightValues(): array
	{
		$settingValues = \Yii::$app->params['settings']['flight_baggage_size_values'] ?? [];
        foreach ($settingValues as $value) {
            $heights = explode('/', $value);
            $result[] = sprintf('UP TO %d LINEAR INCHES/%d LINEAR CENTIMETERS', $heights[0], $heights[1]);
		}
		return $result ?? [];
	}

    /**
     * @return array
     */
    public static function getBaggageWeightValues(): array
	{
		$settingValues = \Yii::$app->params['settings']['flight_baggage_weight_values'] ?? [];
        foreach ($settingValues as $value) {
            $heights = explode('/', $value);
            $result[] = sprintf('UP TO %d POUNDS/%d KILOGRAMS', $heights[0], $heights[1]);
		}
		return $result ?? [];
	}
}