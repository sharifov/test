<?php

namespace modules\hotel\models;

use modules\hotel\models\query\HotelListQuery;
use modules\hotel\src\entities\hotelList\serializer\HotelListSerializer;
use sales\entities\EventTrait;
use sales\entities\serializer\Serializable;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "hotel_list".
 *
 * @property int $hl_id
 * @property int|null $hl_code
 * @property string|null $hl_hash_key
 * @property string $hl_name
 * @property string|null $hl_star
 * @property string|null $hl_category_name
 * @property string|null $hl_destination_code
 * @property string|null $hl_destination_name
 * @property string|null $hl_zone_name
 * @property int|null $hl_zone_code
 * @property string|null $hl_country_code
 * @property string|null $hl_state_code
 * @property string|null $hl_description
 * @property string|null $hl_address
 * @property string|null $hl_postal_code
 * @property string|null $hl_city
 * @property string|null $hl_email
 * @property string|null $hl_web
 * @property string|null $hl_phone_list
 * @property string|null $hl_image_list
 * @property string|null $hl_image_base_url
 * @property string|null $hl_board_codes
 * @property string|null $hl_segment_codes
 * @property float|null $hl_latitude
 * @property float|null $hl_longitude
 * @property int|null $hl_ranking
 * @property string|null $hl_service_type
 * @property string|null $hl_last_update
 * @property string|null $hl_created_dt
 * @property string|null $hl_updated_dt
 *
 * @property HotelQuote[] $hotelQuotes
 */
class HotelList extends \yii\db\ActiveRecord implements Serializable
{
    use EventTrait;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'hotel_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hl_code', 'hl_zone_code', 'hl_ranking'], 'integer'],
            [['hl_name'], 'required'],
            [['hl_description', 'hl_address'], 'string'],
            [['hl_phone_list', 'hl_image_list', 'hl_board_codes', 'hl_segment_codes', 'hl_last_update', 'hl_created_dt', 'hl_updated_dt'], 'safe'],
            [['hl_latitude', 'hl_longitude'], 'number'],
            [['hl_hash_key'], 'string', 'max' => 32],
            [['hl_name', 'hl_destination_name', 'hl_zone_name', 'hl_city', 'hl_web'], 'string', 'max' => 150],
            [['hl_star'], 'string', 'max' => 2],
            [['hl_category_name'], 'string', 'max' => 40],
            [['hl_destination_code', 'hl_country_code', 'hl_state_code'], 'string', 'max' => 5],
            [['hl_postal_code'], 'string', 'max' => 10],
            [['hl_email', 'hl_image_base_url'], 'string', 'max' => 160],
            [['hl_service_type'], 'string', 'max' => 30],
            [['hl_code'], 'unique'],
            [['hl_hash_key'], 'unique'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'hl_id' => 'ID',
            'hl_code' => 'Code',
            'hl_hash_key' => 'Hash Key',
            'hl_name' => 'Name',
            'hl_star' => 'Star',
            'hl_category_name' => 'Category Name',
            'hl_destination_code' => 'Destination Code',
            'hl_destination_name' => 'Destination Name',
            'hl_zone_name' => 'Zone Name',
            'hl_zone_code' => 'Zone Code',
            'hl_country_code' => 'Country Code',
            'hl_state_code' => 'State Code',
            'hl_description' => 'Description',
            'hl_address' => 'Address',
            'hl_postal_code' => 'Postal Code',
            'hl_city' => 'City',
            'hl_email' => 'Email',
            'hl_web' => 'Web',
            'hl_phone_list' => 'Phone List',
            'hl_image_list' => 'Image List',
            'hl_image_base_url' => 'Image Base Url',
            'hl_board_codes' => 'Board Codes',
            'hl_segment_codes' => 'Segment Codes',
            'hl_latitude' => 'Latitude',
            'hl_longitude' => 'Longitude',
            'hl_ranking' => 'Ranking',
            'hl_service_type' => 'Service Type',
            'hl_last_update' => 'Last Update',
            'hl_created_dt' => 'Created Dt',
            'hl_updated_dt' => 'Updated Dt',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['hl_created_dt', 'hl_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['hl_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }


    /**
     * @return ActiveQuery
     */
    public function getHotelQuotes(): ActiveQuery
    {
        return $this->hasMany(HotelQuote::class, ['hq_hotel_list_id' => 'hl_id']);
    }

    /**
     * @return HotelListQuery the active query used by this AR class.
     */
    public static function find(): HotelListQuery
    {
        return new HotelListQuery(static::class);
    }

    /**
     * @param string $code
     * @return string
     */
    public static function getHashKey(string $code): string
    {
        return md5($code);
    }

    /**
     * @param array $data
     * @return static
     */
    public static function findOrCreateByData(array $data): self
    {

        /*
             'categoryName' => '3 STARS'
            'destinationName' => 'Cadiz / Jerez'
            'zoneName' => 'Cádiz'
            'minRate' => 123.12
            'maxRate' => 154.62
            'currency' => 'USD'
            'code' => 147358
            'name' => 'Patagonia Sur'
            'description' => 'Opened in 2009 and offering an unbeatable location in the old town of Cadiz, this beautiful property is just a few minutes\' walk from the most important tourist sites in this vibrant city, including the magnificent Cathedral, located just a few steps away, Plaza Candelaria square and only 100 metres from the Town Hall. Those eager to discover all the hidden treasures of this beautiful region will find the central railway station and the bus terminal 400 metres from this stylish establishment. Simplicity and comfort are the main features of the different rooms, decorated in soothing tones to create a relaxing atmosphere in which guests may feel right at home. They all come with wooden floors and oak furniture. Those most demanding customers may prefer the luxurious penthouse with a large private sun terrace. A delicious breakfast buffet is served daily where travellers may find a choice of delicious fresh products.'
            'countryCode' => 'ES'
            'stateCode' => '11'
            'destinationCode' => 'CAD'
            'zoneCode' => 99
            'latitude' => 36.53034
            'longitude' => -6.294385
            'categoryCode' => '3EST'
            'categoryGroupCode' => 'GRUPO3'
            'boardCodes' => [
                0 => 'BB'
                1 => 'RO'
            ]
            'segmentCodes' => []
            'address' => 'Calle Cobos,11  '
            'postalCode' => '11005'
            'city' => 'CADIZ'
            'email' => 'reservas@hotelpatagoniasur.com'
            'license' => 'H/CA/01216'
            'phones' => [
                0 => [
                    'type' => 'PHONEBOOKING'
                    'number' => '0034956134261'
                ]
                1 => [
                    'type' => 'PHONEHOTEL'
                    'number' => '856174647'
                ]
                2 => [
                    'type' => 'FAXNUMBER'
                    'number' => '856174320'
                ]
                ]
                'images' => [
                    0 => [
                        'url' => '14/147358/147358a_hb_l_001.jpg'
                        'type' => 'COM'
                    ]
                    ],
                    ]
                'lastUpdate' => '2019-12-02'
                's2C' => '2*'
                'ranking' => 1
                'serviceType' => 'HOTELBEDS'
         */

        $hotel = self::findOne(['hl_code' => $data['code']]);

        if (!$hotel) {
            $hotel = new self();
            $hotel->hl_name = $data['name'];
            $hotel->hl_code = $data['code'];
            $hotel->hl_hash_key = self::getHashKey($data['code']);
            $hotel->hl_address = $data['address'] ?? null;
            $hotel->hl_category_name = $data['categoryName'] ?? null;
            $hotel->hl_destination_name = $data['destinationName'] ?? null;
            $hotel->hl_destination_code = $data['destinationCode'] ?? null;

            $hotel->hl_country_code = $data['countryCode'] ?? null;
            $hotel->hl_state_code = $data['stateCode'] ?? null;
            $hotel->hl_postal_code = $data['postalCode'] ?? null;
            $hotel->hl_zone_code = $data['zoneCode'] ?? null;
            $hotel->hl_zone_name = $data['zoneName'] ?? null;

            $hotel->hl_city = $data['city'] ?? null;
            $hotel->hl_email = $data['email'] ?? null;

            $hotel->hl_phone_list       = isset($data['phones']) && $data['phones'] ? json_encode($data['phones']) : null;
            $hotel->hl_image_list       = isset($data['images']) && $data['images'] ? json_encode($data['images']) : null;
            $hotel->hl_segment_codes    = isset($data['segmentCodes']) && $data['segmentCodes'] ? json_encode($data['segmentCodes']) : null;
            $hotel->hl_board_codes      = isset($data['boardCodes']) && $data['boardCodes'] ? json_encode($data['boardCodes']) : null;

            $hotel->hl_description = $data['description'] ?? null;
            $hotel->hl_latitude = $data['latitude'] ?? null;
            $hotel->hl_longitude = $data['longitude'] ?? null;

            $hotel->hl_last_update = isset($data['lastUpdate']) && $data['lastUpdate'] ? date('Y-m-d', strtotime($data['lastUpdate'])) : null;
            $hotel->hl_star = isset($data['s2C']) ? substr($data['s2C'], 2) : null;
            $hotel->hl_ranking = null;
            $hotel->hl_service_type = $data['serviceType'] ?? null;

            if (!$hotel->save()) {
                Yii::error(VarDumper::dumpAsString($hotel->errors), 'Model:HotelList:findOrCreateByData:HotelList:save');
            }
        }
        return $hotel;
    }

    public function serialize(): array
    {
        return (new HotelListSerializer($this))->getData();
    }
}
