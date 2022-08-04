<?php

use modules\smartLeadDistribution\src\objects\flight\FlightSegmentLeadRatingObject;
use modules\smartLeadDistribution\src\services\SmartLeadDistributionService;
use modules\smartLeadDistribution\src\SmartLeadDistribution;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220729_114447_add_destination_parameter_to_lead_rating_parameter_table
 */
class m220729_114447_add_destination_parameter_to_lead_rating_parameter_table extends Migration
{
    private const BASE_PARAMETERS = [
        [
            'object' => SmartLeadDistribution::OBJ_FLIGHT_SEGMENT,
            'attribute' => FlightSegmentLeadRatingObject::FIELD_DESTINATION_COUNTRY,
            'points' => 10,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"flight/flightSegment/flightSegment.destination_country","field":"flightSegment.destination_country","type":"string","input":"select","operator":"in","value":["Albania","Algeria","Angola","Argentina","Australia","Austria","Belarus","Belgium","Benin","Bosnia and Herzegovina","Botswana","Brazil","Bulgaria","Burkina Faso","Burundi","Cabo Verde","Cameroon","Central African Republic","Chad","Comoros","Croatia","Czech Republic","Denmark","Djibouti","Egypt","Equatorial Guinea","Eritrea","Estonia","Eswatini","Ethiopia","Finland","France","French Polynesia","Gabon","Gambia","Germany","Ghana","Greece","Guinea","Guinea-Bissau","Hungary","Iceland","Ireland","Italy","Kenya","Latvia","Lesotho","Liberia","Libya","Lithuania","Luxembourg","Madagascar","Malawi","Maldives","Mali","Malta","Mauritania","Mauritius","Moldova","Monaco","Montenegro","Morocco","Mozambique","Namibia","Netherlands","Niger","Nigeria","North Macedonia","Norway","Poland","Portugal","Romania","Russia","Rwanda","Sao Tome and Principe","Senegal","Serbia","Seychelles","Sierra Leone","Slovakia","Slovenia","Somalia","South Africa","South Sudan","Spain","Sweden","Switzerland","Tanzania","Togo","Tunisia","Uganda","Ukraine","United Kingdom","United States","Zambia","Zimbabwe"]}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_FLIGHT_SEGMENT,
            'attribute' => FlightSegmentLeadRatingObject::FIELD_DESTINATION_COUNTRY,
            'points' => 7,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"flight/flightSegment/flightSegment.destination_country","field":"flightSegment.destination_country","type":"string","input":"select","operator":"in","value":["Afghanistan","Bangladesh","Bhutan","Brunei","Cambodia","Canada","India","Indonesia","Laos","Malaysia","Maldives","Myanmar","Nepal","Pakistan","Philippines","Singapore","Sri Lanka","Thailand","Timor Leste","Vietnam"]}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_FLIGHT_SEGMENT,
            'attribute' => FlightSegmentLeadRatingObject::FIELD_DESTINATION_COUNTRY,
            'points' => 5,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"flight/flightSegment/flightSegment.destination_country","field":"flightSegment.destination_country","type":"string","input":"select","operator":"not_in","value":["Albania","Algeria","Angola","Argentina","Australia","Austria","Belarus","Belgium","Benin","Bosnia and Herzegovina","Botswana","Brazil","Bulgaria","Burkina Faso","Burundi","Cabo Verde","Cameroon","Central African Republic","Chad","Comoros","Croatia","Czech Republic","Denmark","Djibouti","Egypt","Equatorial Guinea","Eritrea","Estonia","Eswatini","Ethiopia","Finland","France","French Polynesia","Gabon","Gambia","Germany","Ghana","Greece","Guinea","Guinea-Bissau","Hungary","Iceland","Ireland","Italy","Kenya","Latvia","Lesotho","Liberia","Libya","Lithuania","Luxembourg","Madagascar","Malawi","Maldives","Mali","Malta","Mauritania","Mauritius","Moldova","Monaco","Montenegro","Morocco","Mozambique","Namibia","Netherlands","Niger","Nigeria","North Macedonia","Norway","Poland","Portugal","Romania","Russia","Rwanda","Sao Tome and Principe","Senegal","Serbia","Seychelles","Sierra Leone","Slovakia","Slovenia","Somalia","South Africa","South Sudan","Spain","Sweden","Switzerland","Tanzania","Togo","Tunisia","Uganda","Ukraine","United Kingdom","United States","Zambia","Zimbabwe","Afghanistan","Bangladesh","Bhutan","Brunei","Cambodia","Canada","India","Indonesia","Laos","Malaysia","Maldives","Myanmar","Nepal","Pakistan","Philippines","Singapore","Sri Lanka","Thailand","Timor Leste","Vietnam"]}],"not":false,"valid":true}'
        ],
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            foreach (self::BASE_PARAMETERS as $parameter) {
                SmartLeadDistributionService::addParameter(
                    $parameter['object'],
                    $parameter['attribute'],
                    $parameter['points'],
                    $parameter['conditionJson']
                );
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220729_114447_add_destination_parameter_to_lead_rating_parameter_table:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            foreach (self::BASE_PARAMETERS as $parameter) {
                SmartLeadDistributionService::deleteParameter(
                    $parameter['object'],
                    $parameter['attribute'],
                    $parameter['points']
                );
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220729_114447_add_destination_parameter_to_lead_rating_parameter_table:safeDown:Throwable');
        }
    }
}
