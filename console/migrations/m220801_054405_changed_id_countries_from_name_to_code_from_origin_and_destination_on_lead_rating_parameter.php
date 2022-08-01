<?php

use yii\db\Migration;

/**
 * Class m220801_054405_changed_id_countries_from_name_to_code_from_origin_and_destination_on_lead_rating_parameter
 */
class m220801_054405_changed_id_countries_from_name_to_code_from_origin_and_destination_on_lead_rating_parameter extends Migration
{
    public function safeUp()
    {
        $parameters = \modules\smartLeadDistribution\src\entities\LeadRatingParameter::find()
            ->where([
                'AND',
                ['lrp_object' => 'flightSegment'],
                ['IN', 'lrp_attribute', ['flightSegment.origin_country', 'flightSegment.destination_country']],
            ])->all();

        foreach ($parameters as $parameter) {
            $json = \yii\helpers\Json::decode($parameter->lrp_condition_json);

            if (isset($json['rules'][0]['value'])) {
                $countryList = [];
                foreach ($json['rules'][0]['value'] as $country) {
                    $airport = \common\models\Airports::find()
                        ->where(['country' => $country])
                        ->limit(1)
                        ->one();

                    if ($airport !== null) {
                        $countryList[] = $airport->a_country_code;
                    }
                }
                $json['rules'][0]['value'] = $countryList;

                $parameter->lrp_condition_json = \yii\helpers\Json::encode($json);
                $parameter->save();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $parameters = \modules\smartLeadDistribution\src\entities\LeadRatingParameter::find()
            ->where([
                'AND',
                ['lrp_object' => 'flightSegment'],
                ['IN', 'lrp_attribute', ['flightSegment.origin_country', 'flightSegment.destination_country']],
            ])->all();

        foreach ($parameters as $parameter) {
            $json = \yii\helpers\Json::decode($parameter->lrp_condition_json);

            if (isset($json['rules'][0]['value'])) {
                $countryList = [];
                foreach ($json['rules'][0]['value'] as $countryCode) {
                    $airport = \common\models\Airports::find()
                        ->where(['a_country_code' => $countryCode])
                        ->limit(1)
                        ->one();

                    if ($airport !== null) {
                        $countryList[] = $airport->country;
                    }
                }
                $json['rules'][0]['value'] = $countryList;

                $parameter->lrp_condition_json = \yii\helpers\Json::encode($json);
                $parameter->save();
            }
        }
    }
}
