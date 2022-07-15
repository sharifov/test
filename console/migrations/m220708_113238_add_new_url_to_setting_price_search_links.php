<?php

use yii\db\Migration;
use yii\db\Query;
use yii\helpers\Json;

/**
 * Class m220708_113238_add_new_url_to_setting_price_search_links
 */
class m220708_113238_add_new_url_to_setting_price_search_links extends Migration
{
    private const LINK_KEY = 'price_line';
    private const LINK_NAME = 'PriceLine';
    private const SETTING_KEY = 'price_research_links';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $newLinkData = [
            'name' => self::LINK_NAME,
            'enabled' => true,
            'url' => 'https://www.priceline.com/m/fly/',
            'types' => [
                'oneTrip' => 'search/{%origin%}-{%destination%}-{%departure_date%}/?cabin-class={%cabin_type%}&num-adults={%adults_count%}{%children_sub_query%}&sbsroute=slice1&search-type=00',
                'roundTrip' => 'search/{%origin%}-{%destination%}-{%first_departure_date%}/{%destination%}-{%origin%}-{%second_departure_date%}/?cabin-class={%cabin_type%}&no-date-search=false&num-adults={%adults_count%}{%children_sub_query%}&sbsroute=slice1&search-type=0000',
                'multiCity' => 'search/{%itinerary_part%}?cabin-class={%cabin_type%}&no-date-search=false&num-adults={%adults_count%}{%children_sub_query%}&sbsroute=slice1&search-type=0000',
            ],
            'multiCityItineraryPattern' => '{%origin%}-{%destination%}-{%departure_date%}/',
            'dateFormat' => 'YYYYMMdd',
            'multiCityDateFormat' => 'YYYYMMdd',
            'cabinClassMappings' => [
                'E' => 'ECO',
                'P' => 'PEC',
                'B' => 'BUS',
                'F' => 'FST',
            ],
            'childrenParameterType' => 'quantitative',
            'childrenSubQueryPart' => '&num-children={%children_count%}&num-infants={%infants_count%}',
        ];


        try {
            $setting = $this->findSetting();
            $existsData = Json::decode($setting['s_value'], true);
            $existsData[self::LINK_KEY] = $newLinkData;

            $this->updateValue($existsData);

            Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');

            if (Yii::$app->cache) {
                Yii::$app->cache->flush();
            }
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220708_113238_add_new_url_to_setting_price_search_links:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $setting = $this->findSetting();
            $links = Json::decode($setting['s_value'], true);

            foreach ($links as $key => $link) {
                if ($key === self::LINK_KEY) {
                    unset($links[$key]);
                }
            }

            $this->updateValue($links);

            Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');

            if (Yii::$app->cache) {
                Yii::$app->cache->flush();
            }
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220708_113238_add_new_url_to_setting_price_search_links:safeUp:Throwable'
            );
        }
    }

    private function findSetting(): array
    {
        $setting = (new Query())
            ->select('*')
            ->from('{{%setting}}')
            ->where(['s_key' => self::SETTING_KEY])
            ->one();

        if ($setting === false) {
            throw new \RuntimeException('Setting with key ' . self::SETTING_KEY . ' not found');
        }

        return $setting;
    }

    private function updateValue(array $value): void
    {
        $this->update(
            '{{%setting}}',
            ['s_value' => Json::encode($value)],
            ['s_key' => self::SETTING_KEY],
        );
    }
}
