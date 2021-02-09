<?php

namespace sales\model\airportLang\service;

use common\models\Airports;
use sales\model\airportLang\entity\AirportLang;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class AirportLangService
 */
class AirportLangService
{
    public const SERVICE_ENDPOINT = 'airport/export/localized';
    public const CACHE_KEY = 'lastUpdatedAirportLang';
    public const CACHE_DURATION = 60 * 60 * 48;
    public const PAGE_LIMIT = 1000;

    public static function getInfo(): array
    {
        $result = [
            'error' => null,
            'lastModified' => 0,
            'total' => 0,
            'allPages' => 0,
        ];
        $responseData = Yii::$app->travelServices->airportExportLocalized(0, 1);

        if ($responseData['error']) {
            return $result['error'] = $responseData['error'];
        }

        $result['total'] = ArrayHelper::getValue($responseData, 'data.Total', 0);
        $result['lastModified'] = ArrayHelper::getValue($responseData, 'data.LastModified', 0);
        $result['allPages'] = ceil($result['total'] / self::PAGE_LIMIT);
        return $result;
    }

    public static function synchronization(
        int $lastUpdate = 0,
        int $pageLimit = 99999,
        int $pageIndex = 0,
        string $lang = '',
        string $format = 'json'
    ): array {
        $data = [
            'info' => [],
            'created' => [],
            'updated' => [],
            'errored' => [],
            'lastModified' => 0,
            'processed' => 0,
            'disabled' => 0,
            'error' => false
        ];

        $responseData = Yii::$app->travelServices->airportExportLocalized($lastUpdate, $pageLimit, $pageIndex, $lang, $format);

        if ($responseData) {
            if ($lastModified = ArrayHelper::getValue($responseData, 'data.LastModified')) {
                $data['lastModified'] = $lastModified;
            }

            if ($responseData['error']) {
                $data['error'] = $responseData['error'];
            } elseif (!empty($responseData['data']['Data'])) {
                foreach ($responseData['data']['Data'] as $item) {
                    if (empty($item['Iata'])) {
                        continue;
                    }
                    if (self::isDisabled($item)) {
                        $data['disabled'] ++ ;
                        continue;
                    }

                    $airportLangObjects = [];

                    if ($names = ArrayHelper::getValue($item, 'Names')) {
                        foreach ($names as $name) {
                            if (isset($name['Language'], $name['Name'])) {
                                if ($airportLang = self::getByIataAndLang($item['Iata'], $name['Language'], $airportLangObjects)) {
                                    if ($airportLang->ail_name !== $name['Name']) {
                                        $currentName = $airportLang->ail_name;
                                        $airportLang->ail_name = $name['Name'];

                                        if (!$airportLang->save()) {
                                            $data['errored'][] = $item['Iata'];
                                            Yii::error(
                                                ['data' => $item, 'errors' => $airportLang->errors],
                                                'AirportLangService:synchronization:update:name'
                                            );
                                        } else {
                                            $data['updated'][] = $item['Iata'] . ' Name : ' . $currentName . ' => ' . $name['Name'];
                                        }
                                    }
                                } else {
                                    $airportLang = new AirportLang();
                                    $airportLang->ail_iata = $item['Iata'];
                                    $airportLang->ail_lang = $name['Language'];
                                    $airportLang->ail_name = $name['Name'];

                                    if (!$airportLang->save()) {
                                        $data['errored'][] = $item['Iata'];
                                        Yii::error(
                                            ['data' => $item, 'errors' => $airportLang->errors],
                                            'AirportLangService:synchronization:created:byName'
                                        );
                                    } else {
                                        $data['created'][] = $item['Iata'];
                                    }
                                }
                            }
                        }
                    }

                    if ($countryNames = ArrayHelper::getValue($item, 'CountryNames')) {
                        foreach ($countryNames as $countryName) {
                            if (isset($countryName['Language'], $countryName['Name'])) {
                                if ($airportLang = self::getByIataAndLang($item['Iata'], $countryName['Language'], $airportLangObjects)) {
                                    if ($airportLang->ail_country !== $countryName['Name']) {
                                        $currentCountry = $airportLang->ail_country;
                                        $airportLang->ail_country = $countryName['Name'];

                                        if (!$airportLang->save()) {
                                            $data['errored'][] = $item['Iata'];
                                            Yii::error(
                                                ['data' => $item, 'errors' => $airportLang->errors],
                                                'AirportLangService:synchronization:update:country'
                                            );
                                        } else {
                                            $data['updated'][] = $item['Iata'] . ' Country : ' . $currentCountry . ' => ' . $countryName['Name'];
                                        }
                                    }
                                } else {
                                    $airportLang = new AirportLang();
                                    $airportLang->ail_iata = $item['Iata'];
                                    $airportLang->ail_lang = $countryName['Language'];
                                    $airportLang->ail_country = $countryName['Name'];

                                    if (!$airportLang->save()) {
                                        $data['errored'][] = $item['Iata'];
                                        Yii::error(
                                            ['data' => $item, 'errors' => $airportLang->errors],
                                            'AirportLangService:synchronization:created:byCountry'
                                        );
                                    } else {
                                        $data['created'][] = $item['Iata'];
                                    }
                                }
                            }
                        }
                    }

                    if ($locationNames = ArrayHelper::getValue($item, 'LocationNames')) {
                        foreach ($locationNames as $locationName) {
                            if (isset($locationName['Language'], $locationName['Name'])) {
                                if ($airportLang = self::getByIataAndLang($item['Iata'], $locationName['Language'], $airportLangObjects)) {
                                    if ($airportLang->ail_city !== $locationName['Name']) {
                                        $currentCity = $airportLang->ail_city;
                                        $airportLang->ail_city = $locationName['Name'];

                                        if (!$airportLang->save()) {
                                            $data['errored'][] = $item['Iata'];
                                            Yii::error(
                                                ['data' => $item, 'errors' => $airportLang->errors],
                                                'AirportLangService:synchronization:update:city'
                                            );
                                        } else {
                                            $data['updated'][] = $item['Iata'] . ' City : ' . $currentCity . ' => ' . $locationName['Name'];
                                        }
                                    }
                                } else {
                                    $airportLang = new AirportLang();
                                    $airportLang->ail_iata = $item['Iata'];
                                    $airportLang->ail_lang = $locationName['Language'];
                                    $airportLang->ail_city = $locationName['Name'];

                                    if (!$airportLang->save()) {
                                        $data['errored'][] = $item['Iata'];
                                        Yii::error(
                                            ['data' => $item, 'errors' => $airportLang->errors],
                                            'AirportLangService:synchronization:created:byCity'
                                        );
                                    } else {
                                        $data['created'][] = $item['Iata'];
                                    }
                                }
                            }
                        }
                    }
                    $data['processed'] ++ ;
                }

                $data['info'][] = 'Processed total IATA codes: ' . ($responseData['data']['Data'] ? count($responseData['data']['Data']) : 0);
                $data['info'][] = 'LastModified (timestamp: ' .
                        $lastModified . ', dateTime: ' . date('Y-m-d H:i:s', (int) $lastModified) . ')';

                if ((int) $lastModified === $lastUpdate) {
                    $data['info'][] = 'Last Modified Data not changed';
                }
            } else {
                $data['info'][] = 'Data Response is empty';
            }
        } else {
            $data['error'] = 'Invalid Data response';
        }

        return $data;
    }

    /**
     * @param string $iata
     * @param string $lang
     * @param array $airportLangObjects
     * @return AirportLang|null
     */
    public static function getByIataAndLang(string $iata, string $lang, array $airportLangObjects): ?AirportLang
    {
        $key = $iata . '-' . $lang;
        if (ArrayHelper::keyExists($key, $airportLangObjects)) {
            return $airportLangObjects[$key];
        }

        if ($airportLang = AirportLang::find()->where(['ail_iata' => $iata, 'ail_lang' => $lang])->one()) {
            $airportLangObjects[$key] = $airportLang;
        }
        return $airportLang;
    }

    /**
     * @param string $iata
     * @param string|null $lang
     * @return string
     */
    public static function getCityByIataAndLang(string $iata, ?string $lang = null): string
    {
        if ($lang && $airportLangCity = self::getAirportLangCity($iata, $lang)) {
            return $airportLangCity;
        }
        if ($airportCity = Airports::find()->select(['city'])->where(['iata' => $iata])->scalar()) {
            return $airportCity;
        }
        return $iata;
    }

    public static function getAirportLangCity(string $iata, ?string $lang = null): ?string
    {
        return AirportLang::find()->select(['ail_city'])->where(['ail_iata' => $iata])->andWhere(['ail_lang' => $lang])->scalar();
    }

    private static function isDisabled(array $item): bool
    {
        return (ArrayHelper::getValue($item, 'IsClosed') === true || ArrayHelper::getValue($item, 'IsDisabled') === true);
    }
}
