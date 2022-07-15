<?php

use yii\db\Migration;
use yii\db\Query;
use yii\helpers\Json;

/**
 * Class m220715_085610_add_google_flight_to_setting_price_research_links
 */
class m220715_085610_add_google_flight_to_setting_price_research_links extends Migration
{
    private const LINK_KEY = 'google_flights';
    private const LINK_NAME = 'Google Flights';
    private const SETTING_KEY = 'price_research_links';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $newLinkData = [
            'name' => self::LINK_NAME,
            'enabled' => true,
            'url' => 'https://www.google.com/travel/flights',
            'types' => [
                'oneTrip' => '?q=Flights {%adults_count%} adults{%children_sub_query%} {%cabin_type%} from {%origin%} to {%destination%} on {%departure_date%} one-way',
                'roundTrip' => '?q=Flights {%adults_count%} adults{%children_sub_query%} {%cabin_type%} to {%destination%} from {%origin%} on {%first_departure_date%} through {%second_departure_date%}',
                'multiCity' => null,
            ],
            'multiCityItineraryPattern' => '{%origin%}-{%destination%}-{%departure_date%}/',
            'dateFormat' => 'YYYY-MM-dd',
            'multiCityDateFormat' => '',
            'cabinClassMappings' => [
                'E' => 'economy',
                'P' => null,
                'B' => 'business class',
                'F' => 'first class',
            ],
            'childrenParameterType' => 'quantitative',
            'childrenSubQueryPart' => ' and {%children_count%} children and {%infants_count%} infants',
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
                'm220715_085610_add_google_flight_to_setting_price_research_links:safeUp:Throwable'
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
                'm220715_085610_add_google_flight_to_setting_price_research_links:safeUp:Throwable'
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
