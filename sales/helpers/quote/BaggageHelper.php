<?php

namespace sales\helpers\quote;

/**
 * Class AddQuoteHelper
 */
class BaggageHelper
{
    public string $heightTemplate = 'UP TO %d LINEAR INCHES/%d LINEAR CENTIMETERS';

	public static function getBaggageHeightValues(): array
	{
		$settingValues = \Yii::$app->params['settings']['flight_baggage_size_values'] ?? [];

        foreach ($settingValues as $value) {
            $heights = explode('/', $value);

            $result[] = sprintf($heightTemplate, $user->username);
		}

		return []; /* TODO::  */
	}
}