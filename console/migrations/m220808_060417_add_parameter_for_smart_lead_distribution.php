<?php

use modules\smartLeadDistribution\src\objects\lead\LeadLeadRatingObject;
use modules\smartLeadDistribution\src\services\SmartLeadDistributionService;
use modules\smartLeadDistribution\src\SmartLeadDistribution;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220808_060417_add_parameter_for_smart_lead_distribution
 */
class m220808_060417_add_parameter_for_smart_lead_distribution extends Migration
{
    private const PARAMETERS = [
        [
            'object' => SmartLeadDistribution::OBJ_LEAD,
            'attribute' => LeadLeadRatingObject::FIELD_CREATED_MINUTES,
            'points' => -5,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"lead/lead.created_minutes","field":"lead.created_minutes","type":"integer","input":"number","operator":">=","value":5}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_LEAD,
            'attribute' => LeadLeadRatingObject::FIELD_CREATED_MINUTES,
            'points' => -10,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"lead/lead.created_minutes","field":"lead.created_minutes","type":"integer","input":"number","operator":">=","value":10}],"not":false,"valid":true}'
        ],
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            foreach (self::PARAMETERS as $parameter) {
                SmartLeadDistributionService::addParameter(
                    $parameter['object'],
                    $parameter['attribute'],
                    $parameter['points'],
                    $parameter['conditionJson']
                );
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220808_060417_add_parameter_for_smart_lead_distribution:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            foreach (self::PARAMETERS as $parameter) {
                SmartLeadDistributionService::deleteParameter(
                    $parameter['object'],
                    $parameter['attribute'],
                    $parameter['points']
                );
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220808_060417_add_parameter_for_smart_lead_distribution:safeDown:Throwable');
        }
    }
}
