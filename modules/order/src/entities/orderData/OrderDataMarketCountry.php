<?php

namespace modules\order\src\entities\orderData;

use common\models\Language;
use sales\model\project\entity\projectLocale\ProjectLocale;
use yii\helpers\VarDumper;
use yii\validators\StringValidator;

/**
 * Class OrderDataMarketCountry
 *
 * @property string|null $marketCountry
 */
class OrderDataMarketCountry
{
    private ?string $marketCountry;

    private function __construct(?string $marketCountry)
    {
        $this->marketCountry = $marketCountry;
    }

    public function getValue(): ?string
    {
        return $this->marketCountry;
    }

    public static function create(int $orderId, $marketCountry, int $projectId, string $action): self
    {
        if (!$marketCountry) {
            \Yii::warning([
                'message' => 'Market country error',
                'error' => 'Is empty',
                'orderId' => $orderId,
                'action' => $action
            ], 'OrderDataMarketCountry');
            return self::default($projectId);
        }

        $stringValidator = new StringValidator(['max' => 2, 'min' => 1]);
        if (!$stringValidator->validate($marketCountry, $error)) {
            \Yii::warning([
                'message' => 'Market country error',
                'marketCountry' => VarDumper::dumpAsString($marketCountry),
                'error' => $error,
                'orderId' => $orderId,
                'action' => $action
            ], 'OrderDataMarketCountry');
            return self::default($projectId);
        }

        if (!array_key_exists($marketCountry, Language::getCountryNames())) {
            \Yii::warning([
                'message' => 'Market country error',
                'marketCountry' => VarDumper::dumpAsString($marketCountry),
                'error' => 'Market country is not found',
                'orderId' => $orderId,
                'action' => $action
            ], 'OrderDataMarketCountry');
            return self::default($projectId);
        }

        return new self($marketCountry);
    }

    public static function default(int $projectId): self
    {
        $marketCountry = null;
        $projectLocale = ProjectLocale::find()->select('pl_market_country')->enabled()->default()->byProject($projectId)->asArray()->one();
        if ($projectLocale && $projectLocale['pl_market_country']) {
            $marketCountry = $projectLocale['pl_market_country'];
        }
        return new self($marketCountry);
    }
}
