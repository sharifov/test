<?php

namespace common\components\ga;

use common\models\VisitorLog;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class GaHelper
 */
class GaHelper
{
    public const TYPE_LEAD = 'lead';
    public const TYPE_QUOTE = 'quote';

    public static function checkSettings(string $type): void
    {
        $gaEnable = (bool) (Yii::$app->params['settings']['ga_enable'] ?? false);
        $gaQbjEnable = $type === self::TYPE_LEAD ? self::checkLeadEnable() : self::checkQuoteEnable() ;

        if (!$gaEnable || !$gaQbjEnable) {
            throw new \DomainException('Service disabled. Please, check GA settings.', -1);
        }
    }

    /**
     * @return bool
     */
    private static function checkLeadEnable(): bool
    {
        return (bool) (Yii::$app->params['settings']['ga_create_lead'] ?? false);
    }

    /**
     * @return bool
     */
    private static function checkQuoteEnable(): bool
    {
        return (bool) (Yii::$app->params['settings']['ga_send_quote'] ?? false);
    }

    /**
     * @param int $clientId
     * @return array|ActiveRecord|null
     */
    public static function getLastGaClientIdByClient(int $clientId)
    {
        return VisitorLog::find()
            ->where(['NOT', ['vl_ga_client_id' => null]])
            ->andWhere(['!=', 'vl_ga_client_id', ''])
            ->andWhere(['vl_client_id' => $clientId])
            ->orderBy(['vl_created_dt' => SORT_DESC])
            ->limit(1)
            ->one();
    }
}