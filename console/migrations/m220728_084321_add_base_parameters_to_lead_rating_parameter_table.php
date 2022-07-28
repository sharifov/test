<?php

use modules\smartLeadDistribution\src\objects\flight\FlightLeadRatingObject;
use modules\smartLeadDistribution\src\objects\flight\FlightSegmentLeadRatingObject;
use modules\smartLeadDistribution\src\objects\lead\LeadLeadRatingObject;
use modules\smartLeadDistribution\src\services\SmartLeadDistributionService;
use modules\smartLeadDistribution\src\SmartLeadDistribution;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220728_084321_add_base_parameters_to_lead_rating_parameter_table
 */
class m220728_084321_add_base_parameters_to_lead_rating_parameter_table extends Migration
{
    private const BASE_PARAMETERS = [
        [
            'object' => SmartLeadDistribution::OBJ_FLIGHT,
            'attribute' => FlightLeadRatingObject::FIELD_PASSENGERS,
            'points' => 10,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"flight/flight.passengers","field":"flight.passengers","type":"integer","input":"number","operator":">=","value":6}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_FLIGHT,
            'attribute' => FlightLeadRatingObject::FIELD_PASSENGERS,
            'points' => 7,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"flight/flight.passengers","field":"flight.passengers","type":"integer","input":"number","operator":">=","value":2},{"id":"flight/flight.passengers","field":"flight.passengers","type":"integer","input":"number","operator":"<=","value":5}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_FLIGHT,
            'attribute' => FlightLeadRatingObject::FIELD_PASSENGERS,
            'points' => 5,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"flight/flight.passengers","field":"flight.passengers","type":"integer","input":"number","operator":"==","value":1}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_FLIGHT,
            'attribute' => FlightLeadRatingObject::FIELD_DATE_PROXIMITY,
            'points' => 10,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"flight/flight.date_proximity","field":"flight.date_proximity","type":"integer","input":"number","operator":"<=","value":14}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_FLIGHT,
            'attribute' => FlightLeadRatingObject::FIELD_DATE_PROXIMITY,
            'points' => 7,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"flight/flight.date_proximity","field":"flight.date_proximity","type":"integer","input":"number","operator":">","value":14},{"id":"flight/flight.date_proximity","field":"flight.date_proximity","type":"integer","input":"number","operator":"<=","value":59}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_FLIGHT,
            'attribute' => FlightLeadRatingObject::FIELD_DATE_PROXIMITY,
            'points' => 5,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"flight/flight.date_proximity","field":"flight.date_proximity","type":"integer","input":"number","operator":">","value":60}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_FLIGHT_SEGMENT,
            'attribute' => FlightSegmentLeadRatingObject::FIELD_ORIGIN_COUNTRY,
            'points' => 10,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"flight/flightSegment/flightSegment.origin_country","field":"flightSegment.origin_country","type":"string","input":"select","operator":"in","value":["United States"]}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_FLIGHT_SEGMENT,
            'attribute' => FlightSegmentLeadRatingObject::FIELD_ORIGIN_COUNTRY,
            'points' => 7,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"flight/flightSegment/flightSegment.origin_country","field":"flightSegment.origin_country","type":"string","input":"select","operator":"in","value":["Canada","France","Germany","Greece","Italy","Portugal","Spain","United Kingdom"]}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_FLIGHT_SEGMENT,
            'attribute' => FlightSegmentLeadRatingObject::FIELD_ORIGIN_COUNTRY,
            'points' => 5,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"flight/flightSegment/flightSegment.origin_country","field":"flightSegment.origin_country","type":"string","input":"select","operator":"not_in","value":["Canada","France","Germany","Greece","Italy","Portugal","Spain","United Kingdom","United States"]}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_LEAD,
            'attribute' => LeadLeadRatingObject::FIELD_TRIP_TYPE,
            'points' => 10,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"lead/lead.trip_type","field":"lead.trip_type","type":"string","input":"select","operator":"in","value":["MC"]}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_LEAD,
            'attribute' => LeadLeadRatingObject::FIELD_TRIP_TYPE,
            'points' => 7,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"lead/lead.trip_type","field":"lead.trip_type","type":"string","input":"select","operator":"in","value":["OW"]}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_LEAD,
            'attribute' => LeadLeadRatingObject::FIELD_TRIP_TYPE,
            'points' => 5,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"lead/lead.trip_type","field":"lead.trip_type","type":"string","input":"select","operator":"in","value":["RT"]}],"not":false,"valid":true}'
        ],
        [
            'object' => SmartLeadDistribution::OBJ_LEAD,
            'attribute' => LeadLeadRatingObject::FIELD_SOURCE,
            'points' => 10,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"lead/lead.source_cid","field":"lead.source_cid","type":"string","input":"select","operator":"in","value":["I1B1L1","A3S6T1","ASSDDI","ACFDCV","AKDINL","AMADCB","AMAMCB","AGOOAD","AFBADS","AKKB16","AKKB32","AKKB36","AKKB79","ASSB16","ASSB36","ASSB32","ASSB79","ARA101","AGDAGC"]}],"not":false,"valid":true}',
        ],
        [
            'object' => SmartLeadDistribution::OBJ_LEAD,
            'attribute' => LeadLeadRatingObject::FIELD_SOURCE,
            'points' => 7,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"lead/lead.source_cid","field":"lead.source_cid","type":"string","input":"select","operator":"in","value":["ASSMDI","AKMINL","AKKDCM","AKKSAD","AKMITI","AKKDSA","AGOOAM","AFBAMD","AGFCBA"]}],"not":false,"valid":true}',
        ],
        [
            'object' => SmartLeadDistribution::OBJ_LEAD,
            'attribute' => LeadLeadRatingObject::FIELD_SOURCE,
            'points' => 5,
            'conditionJson' => '{"condition":"AND","rules":[{"id":"lead/lead.source_cid","field":"lead.source_cid","type":"string","input":"select","operator":"in","value":["ACFMCV","AKDCRT","ATZDCV","ATZMCV"]}],"not":false,"valid":true}',
        ],
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $smartLeadDistributionService = new SmartLeadDistributionService();

            foreach (self::BASE_PARAMETERS as $parameter) {
                $smartLeadDistributionService::addParameter(
                    $parameter['object'],
                    $parameter['attribute'],
                    $parameter['points'],
                    $parameter['conditionJson']
                );
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220728_084321_add_base_parameters_to_lead_rating_parameter_table:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $smartLeadDistributionService = new SmartLeadDistributionService();

            foreach (self::BASE_PARAMETERS as $parameter) {
                $smartLeadDistributionService::deleteParameter(
                    $parameter['object'],
                    $parameter['attribute'],
                    $parameter['points']
                );
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220728_084321_add_base_parameters_to_lead_rating_parameter_table:safeDown:Throwable');
        }
    }
}
