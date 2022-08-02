<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "airports".
 *
 * @property string $name
 * @property string|null $city
 * @property string|null $country
 * @property string $iata
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $timezone
 * @property int|null $dst
 * @property string|null $a_icao
 * @property string|null $a_country_code
 * @property string|null $a_city_code
 * @property string|null $a_state
 * @property float|null $a_rank
 * @property bool|null $a_multicity
 * @property bool|null $a_close
 * @property bool|null $a_disabled
 * @property string|null $a_created_dt
 * @property string|null $a_updated_dt
 * @property int|null $a_created_user_id
 * @property int|null $a_updated_user_id
 *
 * @property Employee $aCreatedUser
 * @property Employee $aUpdatedUser
 * @property QuoteSegment[] $quoteSegments
 * @property-read string $text
 * @property-read string $selection
 * @property-read string $cityName
 * @property QuoteSegment[] $quoteSegments0
 */
class Airports extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'airports';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'iata'], 'required'],

            [['latitude', 'longitude', 'a_rank'], 'number'],
            [['dst', 'a_created_user_id', 'a_updated_user_id'], 'integer'],
            [['a_multicity', 'a_close', 'a_disabled'], 'boolean'],
            [['a_created_dt', 'a_updated_dt'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['city', 'timezone'], 'string', 'max' => 40],
            [['country'], 'string', 'max' => 160],
            [['a_country_code'], 'string', 'max' => 2],
            [['iata', 'a_city_code'], 'string', 'max' => 3],
            [['a_icao'], 'string', 'max' => 4],
            [['a_state'], 'string', 'max' => 80],
            [['iata'], 'unique'],
            [['a_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['a_created_user_id' => 'id']],
            [['a_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['a_updated_user_id' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['a_created_dt', 'a_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['a_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'a_created_user_id',
                'updatedByAttribute' => 'a_updated_user_id',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'city' => 'City',
            'country' => 'Country',
            'iata' => 'IATA',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'timezone' => 'TimeZone',
            'dst' => 'DST',
            'a_icao' => 'ICAO',
            'a_country_code' => 'Country Code',
            'a_city_code' => 'City Code',
            'a_state' => 'State',
            'a_rank' => 'Rank',
            'a_multicity' => 'Multicity',
            'a_close' => 'Close',
            'a_disabled' => 'Disabled',
            'a_created_dt' => 'Created Dt',
            'a_updated_dt' => 'Updated Dt',
            'a_created_user_id' => 'Created User ID',
            'a_updated_user_id' => 'Updated User ID',
        ];
    }

    /**
     * Gets query for [[ACreatedUser]].
     *
     * @return ActiveQuery
     */
    public function getACreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'a_created_user_id']);
    }
    /**
     * Gets query for [[AUpdatedUser]].
     *
     * @return ActiveQuery
     */
    public function getAUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'a_updated_user_id']);
    }

    /**
     * @param string $iata
     * @return Airports|null
     */
    public static function findByIata(string $iata): ?Airports
    {
        return static::findOne(['iata' => $iata]);
    }

    /**
     * Gets query for [[QuoteSegments]].
     *
     * @return ActiveQuery
     */
    public function getQuoteSegments()
    {
        return $this->hasMany(QuoteSegment::class, ['qs_arrival_airport_code' => 'iata']);
    }

    /**
     * Gets query for [[QuoteSegments0]].
     *
     * @return ActiveQuery
     */
    public function getQuoteSegments0()
    {
        return $this->hasMany(QuoteSegment::class, ['qs_departure_airport_code' => 'iata']);
    }

    /**
     * @param array $iata
     * @return array
     */
    public static function getAirportListByIata(array $iata = []): array
    {
        $data = [];
        $airports = self::find()->select(['iata', 'name', 'city', 'country'])->where(['iata' => $iata])->asArray()->all();
        if ($airports) {
            foreach ($airports as $airport) {
                $data[$airport['iata']] = [
                    'name' => $airport['name'],
                    'city' => $airport['city'],
                    'country' => $airport['country']
                ];
            }
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getSelection(): string
    {
        return '(' . $this->iata . ') ' . $this->city;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return '(' . $this->iata . ') ' . $this->name . ', ' . $this->city  . ', ' . $this->country;
    }

    /**
     * @return string
     */
    public function getCityName(): string
    {
        return $this->city;
    }

    /**
     * @param int $duration
     * @return array
     */
    public static function getIataList(int $duration = 5 * 60): array
    {
        return Yii::$app->cacheFile->getOrSet(__FUNCTION__, static function () {
            return ArrayHelper::map(
                self::find()->select(['iata'])->distinct()->orderBy(['iata' => SORT_ASC])->all(),
                'iata',
                'iata'
            );
        }, $duration);
    }

    /**
     * @param int $duration
     * @return array
     */
    public static function getCountryList(int $duration = 5 * 60, string $key = 'country'): array
    {
        $cacheKey = __FUNCTION__ . $key;

        return Yii::$app->cacheFile->getOrSet($cacheKey, static function () use ($key) {
            $selectParams = ['country'];

            if ($key !== $selectParams[0]) {
                $selectParams[] = $key;
            }

            return ArrayHelper::map(
                self::find()->select($selectParams)->distinct()->orderBy(['country' => SORT_ASC])->all(),
                $key,
                'country'
            );
        }, $duration);
    }

    /**
     * @param $iata
     * @return string|null
     */
    public static function getCityByIata($iata): ?string
    {
        if ($airport = self::findByIata($iata)) {
            return $airport->city;
        }
        return null;
    }

    /**
     * @param int $limit
     * @param int $lastUpdated
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public static function synchronization(int $limit = 0, ?int $lastUpdated = 0): array
    {
        $data = [
            'info' => [],
            'created' => [],
            'updated' => [],
            'deleted' => [],
            'errored' => [],
            'error' => false
        ];

        if ($lastUpdated === null) {
            $lastUpdated = 0;
        }

        $airportsData = Yii::$app->travelServices->airportExport($lastUpdated, $limit);

        //$data = $airportsData['data']['Data'] ?? [];

        $utcTime = new \DateTime('now', new \DateTimeZone('UTC'));


        if ($airportsData) {
            if ($airportsData['error']) {
                $data['error'] = 'Error: ' . $airportsData['error'];
            } elseif (!empty($airportsData['data']['Data'])) {
                foreach ($airportsData['data']['Data'] as $item) {
                    if (empty($item['Iata'])) {
                        continue;
                    }

                    $airport = self::find()->where(['iata' => $item['Iata']])->limit(1)->one();
                    $iataCodes = [];

                    if (!$airport) {
                        $airport = new self();
                        $airport->iata = $item['Iata'];
                        $airport->name = $item['Name'] ?? null;
                        $airport->city = $item['CityAscii'] ?? null;
//                        $airport->city = $item['City'];
                        $airport->a_close = (bool) $item['IsClosed'] ?? false;
                        $airport->a_disabled = (bool) $item['IsDisabled'] ?? false;

                        if (!empty($item['Latitude'])) {
                            $airport->latitude = round($item['Latitude'], 14);
                        }
                        if (!empty($item['Longitude'])) {
                            $airport->longitude = round($item['Longitude'], 14);
                        }
                        $airport->country = $item['Country'] ?? null;
                        $airport->timezone = $item['TimezoneId'] ?? null;

                        $airport->a_icao = $item['Icao'] ?? null;
                        $airport->a_country_code = $item['CountryIso2'] ?? null;
                        $airport->a_city_code = $item['CityCode'] ?? null;
                        $airport->a_state = $item['State'] ?? null;
                        $airport->a_rank = $item['Rank'] ?? null;
                        $airport->a_multicity = (bool) $item['IsMulticity'] ?? false;

                        if ($airport->timezone) {
                            $currentTimezone = new \DateTimeZone($airport->timezone);
                            $airport->dst = (int) ($currentTimezone->getOffset($utcTime) / 3600);
                        }

                        $iataCodes[$airport->iata] = $airport->iata;
                        if (!$airport->save()) {
                            $data['errored'][] = $item['Iata'];
                            Yii::error(
                                ['data' => $item, 'errors' => $airport->errors],
                                'Airports:synchronization:Airports:created'
                            );
                        } else {
                            $data['created'][] = $item['Iata'];
                        }
                    } else {
                        $diff = '';
                        if ($airport->name !== $item['Name']) {
                            $airport->name = $item['Name'];
                            $diff .= ', ' . $airport->name . ' => ' . $item['Name'];
                        }

//                        if (!isset($item['City'])) {
//                            VarDumper::dump($item, 10, true);
//                            continue;
//                        }

                        if (isset($item['CityAscii']) && $airport->city !== $item['CityAscii']) {
                            $airport->city = $item['CityAscii'];
                            $diff .= ', ' . $airport->city . ' => ' . $item['CityAscii'];
                        }

//                        if ($airport->city !== $item['City']) {
//                            $airport->city = $item['City'];
//                            $diff .= ', ' . $airport->city . ' => ' . $item['City'];
//                        }

                        if ((bool) $airport->a_close !== (bool) $item['IsClosed']) {
                            $airport->a_close = (bool) $item['IsClosed'];
                            $diff .= ', close = ' . ($airport->a_close ? 'true' : 'false');
                        }

                        if ((bool) $airport->a_disabled !== (bool) $item['IsDisabled']) {
                            $airport->a_disabled = (bool) $item['IsDisabled'];
                            $diff .= ', disabled = ' . ($airport->a_disabled ? 'true' : 'false');
                        }

                        $latitude = $item['Latitude'] ? round($item['Latitude'], 14) : 0;
                        $curLatitude = (string)round($airport->latitude, 14);
                        if ($latitude && $curLatitude !== (string)$latitude) {
                            $diff .= ', lat=' . $curLatitude . ' => ' . $latitude;
                            $airport->latitude = $latitude;
                        }

                        $longitude = $item['Longitude'] ? round($item['Longitude'], 14) : 0;
                        $curLongitude = (string)round($airport->longitude, 14);
                        if ($longitude && $curLongitude !== (string)$longitude) {
                            $diff .= ', long=' . $curLongitude . ' => ' . $longitude;
                            $airport->longitude = $longitude;
                        }
                        if ($airport->country !== $item['Country']) {
                            $airport->country = $item['Country'];
                            $diff .= ', country = ' . $airport->country;
                        }

                        if (!empty($item['Icao']) && $airport->a_icao !== $item['Icao']) {
                            $airport->a_icao = $item['Icao'];
                            $diff .= ', Icao = ' . $airport->a_icao;
                        }

                        if ($airport->a_country_code !== $item['CountryIso2']) {
                            $airport->a_country_code = $item['CountryIso2'];
                            $diff .= ', CountryCode = ' . $airport->a_country_code;
                        }

                        if ($airport->a_city_code !== $item['CityCode']) {
                            $airport->a_city_code = $item['CityCode'];
                            $diff .= ', CityCode = ' . $airport->a_city_code;
                        }

                        if (!empty($item['State']) && $airport->a_state !== $item['State']) {
                            $airport->a_state = $item['State'];
                            $diff .= ', State = ' . $airport->a_state;
                        }

                        if ((float) $airport->a_rank !== (float) $item['Rank']) {
                            $diff .= ', Rank = ' . $airport->a_rank . ' => ' . $item['Rank'];
                            $airport->a_rank = $item['Rank'];
                        }

                        if ((bool) $airport->a_multicity !== (bool) $item['IsMulticity']) {
                            $airport->a_multicity = (bool)$item['IsMulticity'];
                            $diff .= ', multicity = ' . ($airport->a_multicity ? 'true' : 'false');
                        }

                        if (isset($item['TimezoneId']) && $airport->timezone !== $item['TimezoneId']) {
                            $airport->timezone = $item['TimezoneId'];
                            $diff .= ', timezone = ' . $airport->timezone;
                            if ($airport->timezone) {
                                $currentTimezone = new \DateTimeZone($airport->timezone);
                                $dst = (int)($currentTimezone->getOffset($utcTime) / 3600);
                                if ((int)$airport->dst !== $dst) {
                                    $airport->dst = $dst;
                                    $diff .= ', dst = ' . $dst;
                                }
                            }
                        }

                        if (!empty($diff)) {
                            if (!$airport->save()) {
                                $iataCodes[$airport->iata] = $airport->iata;
                                $data['errored'][] = $item['Iata'];
                                Yii::error(
                                    ['data' => $item, 'errors' => $airport->errors],
                                    'Airports:synchronization:Airports:update'
                                );
                            } else {
                                $data['updated'][] = $item['Iata'] . $diff;
                            }
                        }
                    }
                }

                //                    if ($iataCodes) {
//                        $airportsForDelete = self::find()->select(['iata'])->where(['NOT IN', 'iata', $iataCodes])->column();
//                        $data['deleted'] = $airportsForDelete;
//                    }

                $data['info'][] = 'Import total IATA codes: ' . ($airportsData['data']['Data'] ? count($airportsData['data']['Data']) : 0);
                $data['info'][] = 'Old updated: ' . $lastUpdated;
            } else {
                $data['info'][] = 'Data Response is empty';
            }
        } else {
            $data['error'] = 'Invalid Data response';
        }

        return $data;
    }
}
