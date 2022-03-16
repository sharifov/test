<?php

use yii\db\Migration;

/**
 * Class m220311_103648_add_setting_price_search_links
 */
class m220311_103648_add_setting_price_search_links extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $settingCategory = \common\models\SettingCategory::getOrCreateByName('Price link Search');

            $this->insert(
                '{{%setting}}',
                [
                    's_key'         => 'price_research_links',
                    's_name'        => 'Price Search Links',
                    's_type'        => \common\models\Setting::TYPE_ARRAY,
                    's_value'       => json_encode([
                        [
                            'name'                             => 'SkyScanner',
                            'enabled'                          => true,
                            'oneTripUrl'                       => 'https://www.skyscanner.net/transport/flights/{%origin%}/{%destination%}/{%departure_date%}/?adults={%adults_count%}&adultsv2={%adults_count%}&cabinclass={%cabin_type%}{%children_sub_query%}&inboundaltsenabled=false&outboundaltsenabled=false&preferdirects=false&ref=home&rtn=0',
                            'roundTripUrl'                     => 'https://www.skyscanner.net/transport/flights/{%origin%}/{%destination%}/{%first_departure_date%}/{%second_departure_date%}/?adults={%adults_count%}&adultsv2={%adults_count%}&cabinclass={%cabin_type%}{%children_sub_query%}&inboundaltsenabled=false&outboundaltsenabled=false&preferdirects=false&ref=home&rtn=0',
                            'multiCityUrl'                     => 'https://www.skyscanner.net/transport/d/{%itinerary_part%}?adults={%adults_count%}&adultsv2={%adults_count%}&cabinclass={%cabin_type%}{%children_sub_query%}&inboundaltsenabled=false&outboundaltsenabled=false&preferdirects=false&ref=home&rtn=0',
                            'multiCityItineraryPattern'        => '{%origin%}/{%departure_date%}/{%destination%}/',
                            'dateFormat'                       => 'yyMMdd',
                            'multiCityDateFormat'              => 'YYYY-MM-dd',
                            'cabinClassMappings'               => [
                                'E' => 'economy',
                                'P' => 'premiumeconomy',
                                'B' => 'business',
                                'F' => 'first',
                            ],
                            'childrenParameterType'            => 'enumerable',
                            'childrenParameterSeparator'       => '|',
                            'childrenSubQueryPart'             => '&childrenv2={%children_and_infants_count_part%}',
                            'childPaxTypeEnumerableParameter'  => '10',
                            'infantPaxTypeEnumerableParameter' => '0',
                        ],
                        [
                            'name'                             => 'Kayak',
                            'enabled'                          => true,
                            'oneTripUrl'                       => 'https://www.kayak.com/flights/{%origin%}-{%destination%}/{%departure_date%}/{%cabin_type%}{%adults_count%}adults{%children_sub_query%}?sort=price_a',
                            'roundTripUrl'                     => 'https://www.kayak.com/flights/{%origin%}-{%destination%}/{%first_departure_date%}/{%second_departure_date%}/{%cabin_type%}{%adults_count%}adults{%children_sub_query%}?fs=cabin={%cabin_type%}&sort=bestflight_a',
                            'multiCityUrl'                     => 'https://www.kayak.com/flights/{%itinerary_part%}{%cabin_type%}{%adults_count%}adults{%children_sub_query%}?fs=cabin={%cabin_type%}&sort=bestflight_a',
                            'multiCityItineraryPattern'        => '{%origin%}-{%destination%}/{%departure_date%}-h/',
                            'dateFormat'                       => 'YYYY-MM-dd',
                            'multiCityDateFormat'              => 'YYYY-MM-dd',
                            'cabinClassMappings'               => [
                                'E' => '',
                                'P' => 'premium/',
                                'B' => 'business/',
                                'F' => 'first/',
                            ],
                            'childrenParameterType'            => 'enumerable',
                            'childrenParameterSeparator'       => '-',
                            'childrenSubQueryPart'             => '/children-{%children_and_infants_count_part%}',
                            'childPaxTypeEnumerableParameter'  => '11',
                            'infantPaxTypeEnumerableParameter' => '1S',
                        ],
                        [
                            'name'                             => 'Momondo',
                            'enabled'                          => true,
                            'oneTripUrl'                       => 'https://www.momondo.com/flight-search/{%origin%}-{%destination%}/{%departure_date%}/{%cabin_type%}{%adults_count%}adults{%children_sub_query%}?sort=price_a',
                            'roundTripUrl'                     => 'https://www.momondo.com/flight-search/{%origin%}-{%destination%}/{%first_departure_date%}/{%second_departure_date%}{%cabin_type%}{%adults_count%}adults{%children_sub_query%}?sort=price_a',
                            'multiCityUrl'                     => 'https://www.momondo.com/flight-search/{%itinerary_part%}{%cabin_type%}{%adults_count%}adults{%children_sub_query%}?sort=price_a',
                            'multiCityItineraryPattern'        => '{%origin%}-{%destination%}/{%departure_date%}-h/',
                            'dateFormat'                       => 'YYYY-MM-dd',
                            'multiCityDateFormat'              => 'YYYY-MM-dd',
                            'cabinClassMappings'               => [
                                'E' => '',
                                'P' => 'premium/',
                                'B' => 'business/',
                                'F' => 'first/',
                            ],
                            'childrenParameterType'            => 'enumerable',
                            'childrenParameterSeparator'       => '-',
                            'childrenSubQueryPart'             => '/children-{%children_and_infants_count_part%}',
                            'childPaxTypeEnumerableParameter'  => '11',
                            'infantPaxTypeEnumerableParameter' => '1S',
                        ],
                        [
                            'name'                      => 'Justify.com',
                            'enabled'                   => true,
                            'oneTripUrl'                => 'https://www.justfly.com/flight/search?num_adults={%adults_count%}{%children_sub_query%}&seat_class={%cabin_type%}&seg0_date={%departure_date%}&seg0_from={%origin%}&seg0_to={%destination%}&type=oneway',
                            'roundTripUrl'              => 'https://www.justfly.com/flight/search?num_adults={%adults_count%}{%children_sub_query%}&seat_class={%cabin_type%}&seg0_date={%first_departure_date%}&seg0_from={%origin%}&seg0_to={%destination%}&seg1_date={%second_departure_date%}&seg1_from={%destination%}&seg1_to={%origin%}&type=roundtrip',
                            'multiCityUrl'              => 'https://www.justfly.com/flight/search?num_adults={%adults_count%}{%children_sub_query%}&seat_class={%cabin_type%}{%itinerary_part%}&type=multi',
                            'multiCityItineraryPattern' => '&seg{%segment_index%}_date={%departure_date%}&seg{%segment_index%}_from={%origin%}&seg{%segment_index%}_to={%destination%}',
                            'dateFormat'                => 'YYYY-MM-dd',
                            'multiCityDateFormat'       => 'YYYY-MM-dd',
                            'cabinClassMappings'        => [
                                'E' => 'Economy',
                                'P' => 'EconomyPremium',
                                'B' => 'Business',
                                'F' => 'First',
                            ],
                            'childrenParameterType'     => 'quantitative',
                            'childrenSubQueryPart'      => '&num_children={%children_count%}&num_infants={%infants_count%}&num_infants_lap=0',
                        ],
                    ]),
                    's_updated_dt'  => date('Y-m-d H:i:s'),
                    's_category_id' => $settingCategory->sc_id,
                ]
            );
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220311_103648_add_setting_price_search_links:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', [
            'IN',
            's_key',
            [
                'price_research_links',
            ]
        ]);
    }
}
