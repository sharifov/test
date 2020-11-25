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
 * @property int|null $a_multicity
 * @property int|null $a_close
 * @property int|null $a_disabled
 * @property string|null $a_created_dt
 * @property string|null $a_updated_dt
 * @property int|null $a_created_user_id
 * @property int|null $a_updated_user_id
 *
 * @property Employee $aCreatedUser
 * @property Employee $aUpdatedUser
 * @property QuoteSegment[] $quoteSegments
 * @property QuoteSegment[] $quoteSegments0
 */
class Airports extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'airports';
    }

    /**
     * {@inheritdoc}
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
            [['city', 'country', 'timezone'], 'string', 'max' => 40],
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
     * {@inheritdoc}
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
    public static function getCountryList(int $duration = 5 * 60): array
    {
        return Yii::$app->cacheFile->getOrSet(__FUNCTION__, static function () {
            return ArrayHelper::map(
                self::find()->select(['country'])->distinct()->orderBy(['country' => SORT_ASC])->all(),
                'country',
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
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public static function synchronization(int $limit = 0): array
    {
        $data = [
            'created' => [],
            'updated' => [],
            'errored' => [],
            'error' => false
        ];

        $airportsData = Yii::$app->travelServices->airportExport(0, $limit);

        //$data = $airportsData['data']['Data'] ?? [];

        $utcTime = new \DateTime('now', new \DateTimeZone('UTC'));


        if ($airportsData) {
            if ($airportsData['error']) {
                $data['error'] = 'Error: ' . $airportsData['error'];
            } else {
                if (!empty($airportsData['data']['Data'])) {
                    foreach ($airportsData['data']['Data'] as $item) {
                        if (empty($item['Iata'])) {
                            continue;
                        }
                        $airport = self::findOne(['iata' => $item['Iata']]);

                        if (!$airport) {
                            $airport = new self();
                            $airport->iata = $item['Iata'];
                            $airport->name = $item['Name'];
                            $airport->city = $item['CityAscii'];
                            $data['created'][] = $item['Iata'];
                        } else {
                            $data['updated'][] = $item['Iata'];
                        }

                        $airport->latitude = $item['Latitude'];
                        $airport->longitude = $item['Longitude'];
                        $airport->country = $item['Country'];
                        $airport->timezone = $item['TimezoneId'];

                        $airport->a_icao = $item['Icao'] ?? null;
                        $airport->a_country_code = $item['CountryIso2'];
                        $airport->a_city_code = $item['CityCode'];
                        $airport->a_state = $item['State'] ?? null;
                        $airport->a_rank = $item['Rank'];
                        $airport->a_multicity = (bool) $item['IsMulticity'];
                        $airport->a_close = (bool) $item['IsClosed'];
                        $airport->a_disabled = (bool) $item['IsDisabled'];

                        if ($airport->timezone) {
                            $currentTimezone = new \DateTimeZone($airport->timezone);
                            $airport->dst = (int) ($currentTimezone->getOffset($utcTime) / 3600);
                        }

                        if (!$airport->save()) {
                            $data['errored'][] = $item['Iata'];
                            Yii::error(
                                VarDumper::dumpAsString(['data' => $item, 'errors' => $airport->errors]),
                                'Airports:synchronization:Airports:save'
                            );
                        }
                    }
                }
            }
        } else {
            $data['error'] = 'Not found response Data';
        }

        return $data;
    }
}
