<?php

namespace common\components\ga;

use common\models\Lead;
use common\models\VisitorLog;
use sales\helpers\lead\LeadHelper;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class GaHelper
 */
class GaHelper
{
    public const TYPE_LEAD = 'lead';
    public const TYPE_QUOTE = 'quote';

    /**
     * @param string $type
     * @return bool
     */
    public static function checkSettings(string $type): bool
    {
        $gaEnable = (bool) (Yii::$app->params['settings']['ga_enable'] ?? false);
        $gaQbjEnable = $type === self::TYPE_LEAD ? self::checkLeadEnable() : self::checkQuoteEnable() ;

        if (!$gaEnable || !$gaQbjEnable) {
            throw new \DomainException('Service disabled. Please, check Google Analytics settings.', -1);
        }
        return true;
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

    /**
     * @param Lead $lead
     * @return string|null
     */
    public static function getTrackingIdByLead(Lead $lead): ?string
    {
        if ($lead->project && $trackingId = $lead->project->ga_tracking_id) {
            return $trackingId;
        }
        return null;
    }

    /**
     * @param Lead $lead
     * @return string|null
     */
    public static function getClientIdByLead(Lead $lead): ?string
    {
        $visitorLog = self::getLastGaClientIdByClient($lead->client_id);
        if ($visitorLog && $clientId = $visitorLog->vl_ga_client_id) {
            return $clientId;
        }
        return null;
    }

    /**
     * @param array $postData
     * @param Lead $lead
     * @return array
     */
    public static function preparePostData(array $postData, Lead $lead): array
    {
        $postData['cd7'] = $lead->getCabinClassName();
        $postData['cd12'] = '';
        $postData['cd13'] = $lead->source ? $lead->source->cid : '';
        $postData['cd14'] = '';
        $postData['cd15'] = $lead->uid;
        $postData['cm1'] = $lead->adults;
        $postData['cm2'] = $lead->children;
        $postData['cm3'] = $lead->infants;

        return $postData;
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
}