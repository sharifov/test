<?php

namespace sales\model\airline\service;

use common\models\Airline;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class AirportLangService
 */
class AirlineService
{
    public const SERVICE_ENDPOINT = 'airline/export';

    public static function synchronization(int $lastUpdate = 0, int $limit = 99999, bool $ad = false, string $format = 'json'): array
    {
        $data = [
            'created' => [],
            'updated' => [],
            'errored' => [],
            'disabled' => [],
            'lastModified' => 0,
            'processed' => 0,
            'error' => false
        ];

        $responseData = Yii::$app->travelServices->airlineExport($lastUpdate, $limit, $ad, $format);
        $data['lastModified'] = ArrayHelper::getValue($responseData, 'data.LastModified', 0);

        if ($responseData['error']) {
            $data['error'] = $responseData['error'];
            return $data;
        }

        if (!empty($responseData['data']['Data'])) {
            foreach ($responseData['data']['Data'] as $item) {
                if (empty($item['Iata'])) {
                    continue;
                }
                if (ArrayHelper::getValue($item, 'Disabled') === true) {
                    $data['disabled'][] = $item['Iata'];
                    continue;
                }

                if ($airline = Airline::findOne(['iata' => $item['Iata']])) {
                    $diff = '';
                    if (ArrayHelper::keyExists('Name', $item) && $airline->name !== $item['Name']) {
                        $origName = $airline->getOldAttribute('name');
                        $airline->name = $item['Name'];
                        $diff .= ' Name: "' . $origName . '" => "' . $item['Name'] . '"';
                    }
                    if (ArrayHelper::keyExists('Country', $item) && $airline->country !== $item['Country']) {
                        $origCountry = $airline->getOldAttribute('country');
                        $airline->country = $item['Country'];
                        $diff .= ' Country: "' . $origCountry . '" => "' . $item['Country'] . '"';
                    }
                    if (ArrayHelper::keyExists('CountryIso2', $item) && $airline->countryCode !== $item['CountryIso2']) {
                        $origCountryCode = $airline->getOldAttribute('countryCode');
                        $airline->countryCode = $item['CountryIso2'];
                        $diff .= ' CountryCode: "' . $origCountryCode . '" => "' . $item['CountryIso2'] . '"';
                    }
                    if (!empty($diff)) {
                        if (!$airline->save()) {
                            $data['errored'][] = $item['Iata'];
                            Yii::error(
                                ['data' => $item, 'errors' => $airline->errors],
                                'AirlineService:synchronization:update'
                            );
                        } else {
                            $data['updated'][] = '#' . $item['Iata'] . ' - ' .  $diff;
                        }
                    }
                } else {
                    $airline = new Airline();
                    $airline->iata = $item['Iata'];
                    $airline->country = ArrayHelper::getValue($item, 'Country');
                    $airline->countryCode = ArrayHelper::getValue($item, 'CountryIso2');
                    $airline->name = ArrayHelper::getValue($item, 'Name');

                    if (!$airline->save()) {
                        $data['errored'][] = $item['Iata'];
                        Yii::error(
                            ['data' => $item, 'errors' => $airline->errors],
                            'AirlineService:synchronization:created'
                        );
                    } else {
                        $data['created'][] = $item['Iata'];
                    }
                }
            }
        }

        return $data;
    }

    public static function getCountryList(): array
    {
        return ArrayHelper::map(
            Airline::find()
                ->select(['country'])
                ->orderBy(['country' => SORT_ASC])
                ->groupBy(['country'])
                ->asArray()
                ->all(),
            'country',
            'country'
        );
    }

    public static function getCountryCodeList(): array
    {
        return ArrayHelper::map(
            Airline::find()
                ->select(['countryCode'])
                ->orderBy(['countryCode' => SORT_ASC])
                ->groupBy(['countryCode'])
                ->asArray()
                ->all(),
            'countryCode',
            'countryCode'
        );
    }
}
